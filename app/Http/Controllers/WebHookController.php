<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Jobs\NullJob;
use App\Models\Site\Site;
use App\Models\User\User;
use Illuminate\Http\Request;
use App\Jobs\Site\DeploySite;
use App\Models\Server\Server;
use App\Jobs\Server\BackupDatabases;
use App\Jobs\Server\RestartWebServices;
use App\Notifications\Server\ServerLoad;
use App\Notifications\Server\ServerMemory;
use App\Notifications\Server\ServerDiskUsage;
use App\Http\Controllers\Auth\OauthController;
use App\Jobs\Server\SslCertificates\UpdateServerSslCertificate;

class WebHookController extends Controller
{
    const STAT_RETENTION = 50;
    /**
     * @param Request $request
     * @param $siteHashId
     * @return \Illuminate\Http\JsonResponse
     */
    public function deploy(Request $request, $siteHashId)
    {
        $requestData = $request;
        if($request->headers->get('content-type') === "application/x-www-form-urlencoded") {
            $requestData = collect(json_decode($request->getContent(), true));
        }

        $site = Site::with('userRepositoryProvider.repositoryProvider')
            ->where('hash', $siteHashId)
            ->firstOrFail();

        /** @var User $user */
        $user = $site->user;

        $this->checkUsersMaxServers($user);

        if ($user->subscribed() || $user->sites->count() <= 1) {
            $branch = null;

            if (! empty($site->userRepositoryProvider)) {
                switch ($site->userRepositoryProvider->repositoryProvider->provider_name) {
                    case OauthController::GITHUB:
                    case OauthController::GITLAB:
                        $branch = substr($requestData->get('ref'), strrpos($requestData->get('ref'), '/') + 1);
                        break;
                    case OauthController::BITBUCKET:
                        $branch = $requestData->get('push')['changes'][0]['new']['name'];
                        break;
                }
            }
            if (empty($branch) || $site->branch === $branch) {
                \Cache::lock("deploy_hook_lock-{$site->id}")->get(function () use($site) {
                    dispatch(
                        (new DeploySite($site, null))
                            ->onQueue(config('queue.channels.site_deployments'))
                    );
                });
            }

            return response()->json('OK');
        }

        $this->subscriptionToLow('sites');
    }

    /**
     * @param Request $request
     * @param $serverHashId
     * @return \Illuminate\Http\JsonResponse
     */
    public function loadMonitor(Request $request, $serverHashId)
    {
        $server = Server::with('stats')->findOrFail(\Hashids::decode($serverHashId)[0]);

        if ($this->checkServerIp($server, $request)) {
            $this->checkUsersMaxServers($server->user);

            $stats = $this->getStats($request->get('loads'));

            $server = $this->createStats($server);

            if (! empty($stats)) {

                $stats['updated_at'] = Carbon::now()->toIso8601String();

                $loadStats = $server->stats->load_stats;
                $loadStats[] = $stats;

                if (\Cache::lock("stats_$server->id", 5)) {
                    $server->stats->update([
                        'number_of_cpus' => $request->get('cpus'),
                        'load_stats' => array_slice($loadStats, -self::STAT_RETENTION, self::STAT_RETENTION)
                    ]);

                    $server->notify(new ServerLoad($server));
                }

                return response()->json('OK');
            }

            return response()->json('Malformed Data sent', 400);
        }
        return $this->returnInvalidServerIp();
    }

    /**
     * @param Request $request
     * @param $serverHashId
     * @return \Illuminate\Http\JsonResponse
     */
    public function memoryMonitor(Request $request, $serverHashId)
    {
        $server = Server::with('stats')->findOrFail(\Hashids::decode($serverHashId)[0]);

        if ($this->checkServerIp($server, $request)) {
            $this->checkUsersMaxServers($server->user);

            $stats = $this->getStats($request->get('memory'));

            $server = $this->createStats($server);

            if (isset($stats['name'])) {

                $stats['updated_at'] = Carbon::now()->toIso8601String();

                if (empty($stats['available'])) {
                    $stats['available'] = $stats['free'];
                }

                $memoryName = str_replace(':', '', $stats['name']);
                unset($stats['name']);

                $memoryStats = $server->stats->memory_stats;
                $memoryStats[$memoryName][] = $stats;

                if (\Cache::lock("stats_$server->id", 5)) {
                    $server->stats->update([
                        "memory_stats->$memoryName" => array_slice($memoryStats[$memoryName], -self::STAT_RETENTION, self::STAT_RETENTION)
                    ]);

                    $server->notify(new ServerMemory($server, $memoryName));
                }

                return response()->json('OK');
            }

            return response()->json('Malformed Data sent', 400);
        }
        return $this->returnInvalidServerIp();
    }

    /**
     * @param Request $request
     * @param $serverHashId
     * @return \Illuminate\Http\JsonResponse
     */
    public function diskUsageMonitor(Request $request, $serverHashId)
    {
        $server = Server::with('stats')->findOrFail(\Hashids::decode($serverHashId)[0]);

        if ($this->checkServerIp($server, $request)) {

            $this->checkUsersMaxServers($server->user);

            $stats = $this->getStats($request->get('disk_usage'));

            $server = $this->createStats($server);

            if (isset($stats['disk'])) {

                $stats['updated_at'] = Carbon::now()->toIso8601String();

                $diskName = $stats['disk'];
                unset($stats['disk']);
                $diskStats = $server->stats->disk_stats;
                $diskStats[$diskName][] = $stats;

                if (\Cache::lock("stats_$server->id", 5)) {
                    $server->stats->update([
                        "disk_stats->$diskName" => array_slice($diskStats[$diskName], -self::STAT_RETENTION, self::STAT_RETENTION)
                    ]);
                    $server->notify(new ServerDiskUsage($server, $diskName));
                }

                return response()->json('OK');
            }

            return response()->json('Malformed Data sent', 400);
        }
        return $this->returnInvalidServerIp();
    }

    /**
     * @param Request $request
     * @param $serverHashId
     * @return \Illuminate\Http\JsonResponse
     */
    public function serverSslCertificateUpdated(Request $request, $serverHashId)
    {
        /** @var Server $server */
        $server = Server::with('sslCertificates')->findOrFail(\Hashids::decode($serverHashId)[0]);

        if ($this->checkServerIp($server, $request)) {
            $this->checkUsersMaxServers($server->user);

            $chainJobs = [];

            foreach ($server->sslCertificates as $sslCertificate) {
                $chainJobs[] = new UpdateServerSslCertificate($server, $sslCertificate);
            }

            $serversToRestartWebServices = collect();
            foreach($server->sslCertificates->site as $site) {
                $serversToRestartWebServices->push($site->server);
            }

            $serversToRestartWebServices->unique('id')->each(function($server) use($chainJobs) {
                $chainJobs[] = new RestartWebServices($server);
            });

            NullJob::withChain($chainJobs)->dispatch()->allOnQueue(config('queue.channels.server_commands'));


            return response()->json('OK');
        }

        return $this->returnInvalidServerIp();
    }

    /**
     * @param Request $request
     * @param $serverHashId
     * @return \Illuminate\Http\JsonResponse
     */
    public function databaseBackups(Request $request, $serverHashId)
    {
        $server = Server::findOrFail(\Hashids::decode($serverHashId)[0]);

        if ($this->checkServerIp($server, $request)) {
            if ($server->backups_enabled) {
                /** @var User $user */
                $user = $server->user;

                if ($user->subscribed()) {
                    dispatch((
                    new BackupDatabases($server)
                    )->onQueue(
                            config('queue.channels.server_commands')
                        ));

                    return response()->json('OK');
                }

                return response()->json('You must be a subscriber to allow backups', 402);
            }

            return response()->json('Backups Not Enabled on this server', 400);
        }

        return $this->returnInvalidServerIp();
    }

    /**
     * @param User $user
     * @return bool
     */
    private function checkUsersMaxServers(User $user)
    {
        if ($user->role !== 'admin') {
            if (! $user->subscribed()) {
                if ($user->servers->count() > 1) {
                    $this->subscriptionToLow('servers');
                }

                return true;
            }

            $stripePlan = $user->subscription()->active_plan;

            if (str_contains($stripePlan, 'firstmate')) {
                if ($user->servers->count() > 30) {
                    $this->subscriptionToLow('servers');
                }
            }
        }

        return true;
    }

    /**
     * @param $type
     */
    private function subscriptionToLow($type)
    {
        return abort(402, 'Too many '.$type.', please upgrade');
    }

    /**
     * @param $items
     * @return array
     */
    private function getStats($items)
    {
        $stats = [];

        foreach (explode(' ', $items) as $stat) {
            $statParts = explode('=', $stat);

            if (isset($statParts[0]) && isset($statParts[1])) {
                $stats[$statParts[0]] = $statParts[1];
            }
        }

        return $stats;
    }

    /**
     * @param Server $server
     * @param Request $request
     * @return bool
     */
    private function checkServerIp(Server $server, Request $request)
    {
        if ($server->ip === $request->ip() || $request->ip() === "127.0.0.1") {
            return true;
        }
        return false;
    }

    private function returnInvalidServerIp()
    {
        return response()->json('This server IP doesn\'t match whats on record', 400);
    }

    private function createStats(Server $server) {
        if(!$server->relationLoaded('stats')) {
            $server->load('stats');
        }

        if(empty($server->stats)) {
            $server->stats()->create();
            $server->load('stats');
        }
        return $server;
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Site\Site;
use App\Models\User\User;
use Illuminate\Http\Request;
use App\Jobs\Site\DeploySite;
use App\Models\Server\Server;
use App\Jobs\Server\BackupDatabases;
use App\Notifications\Server\ServerLoad;
use App\Notifications\Server\ServerMemory;
use App\Notifications\Server\ServerDiskUsage;
use App\Http\Controllers\Auth\OauthController;
use App\Jobs\Server\SslCertificates\UpdateServerSslCertificate;

class WebHookController extends Controller
{
    /**
     * @param Request $request
     * @param $siteHashId
     * @return \Illuminate\Http\JsonResponse
     */
    public function deploy(Request $request, $siteHashId)
    {
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
                        $branch = substr($request->get('ref'), strrpos($request->get('ref'), '/') + 1);
                        break;
                    case OauthController::BITBUCKET:
                        $branch = $request->get('push')['changes'][0]['new']['name'];
                        break;
                }
            }

            if (empty($branch) || $site->branch === $branch) {
                dispatch(
                    (new DeploySite($site, null))
                        ->onQueue(config('queue.channels.site_deployments'))
                );
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
        $server = Server::findOrFail(\Hashids::decode($serverHashId)[0]);

        if ($this->checkServerIp($server, $request)) {
            $this->checkUsersMaxServers($server->user);

            $stats = $this->getStats($request->get('loads'));

            if (! empty($stats)) {
                $server->update([
                    'stats->cpus' => $request->get('cpus'),
                    'stats->loads' => $stats,
                ]);

                $server->notify(new ServerLoad($server));

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
        $server = Server::findOrFail(\Hashids::decode($serverHashId)[0]);

        if ($this->checkServerIp($server, $request)) {
            $this->checkUsersMaxServers($server->user);

            $memoryStats = $this->getStats($request->get('memory'));

            if (isset($memoryStats['name'])) {
                $server->update([
                    'stats->memory->'.str_replace(':', '', $memoryStats['name']) => $memoryStats,
                ]);

                $server->notify(new ServerMemory($server));

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
        $server = Server::findOrFail(\Hashids::decode($serverHashId)[0]);

        if ($this->checkServerIp($server, $request)) {
            $this->checkUsersMaxServers($server->user);

            $diskStats = $this->getStats($request->get('disk_usage'));

            if (isset($diskStats['disk'])) {
                $server->update([
                    'stats->disk_usage->'.$diskStats['disk'] => $diskStats,
                ]);

                $server->notify(new ServerDiskUsage($server));

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

            foreach ($server->sslCertificates as $sslCertificate) {
                dispatch(new UpdateServerSslCertificate($server, $sslCertificate));
            }
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
}

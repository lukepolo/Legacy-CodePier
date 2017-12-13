<?php

namespace App\Http\Controllers;

use App\Models\Site\Site;
use Illuminate\Http\Request;
use App\Models\Server\Server;
use App\Jobs\Site\DeploySite;
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
                (new DeploySite($site, null, true))
                    ->onQueue(config('queue.channels.server_commands'))
            );
        }

        return response()->json('OK');
    }

    /**
     * @param Request $request
     * @param $serverHashId
     * @return \Illuminate\Http\JsonResponse
     */
    public function loadMonitor(Request $request, $serverHashId)
    {
        $server = Server::findOrFail(\Hashids::decode($serverHashId)[0]);

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

    /**
     * @param Request $request
     * @param $serverHashId
     * @return \Illuminate\Http\JsonResponse
     */
    public function memoryMonitor(Request $request, $serverHashId)
    {
        $server = Server::findOrFail(\Hashids::decode($serverHashId)[0]);

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

    /**
     * @param Request $request
     * @param $serverHashId
     * @return \Illuminate\Http\JsonResponse
     */
    public function diskUsageMonitor(Request $request, $serverHashId)
    {
        $server = Server::findOrFail(\Hashids::decode($serverHashId)[0]);

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

    /**
     * @param $serverHashId
     */
    public function serverSslCertificateUpdated($serverHashId)
    {
        /** @var Server $server */
        $server = Server::with('sslCertificates')->findOrFail(\Hashids::decode($serverHashId)[0]);

        foreach ($server->sslCertificates as $sslCertificate) {
            dispatch(new UpdateServerSslCertificate($server, $sslCertificate));
        }
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
}

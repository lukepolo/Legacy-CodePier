<?php

namespace App\Http\Controllers;

use App\Models\Site\Site;
use Illuminate\Http\Request;
use App\Models\Server\Server;
use App\Notifications\Server\ServerLoad;
use App\Notifications\Server\ServerMemory;
use App\Notifications\Server\ServerDiskUsage;
use App\Http\Controllers\Auth\OauthController;

class WebHookController extends Controller
{
    /**
     * @param Request $request
     * @param $siteHashID
     * @return \Illuminate\Http\JsonResponse
     */
    public function deploy(Request $request, $siteHashID)
    {
        $site = Site::with('userRepositoryProvider.repositoryProvider')
            ->findOrFail(\Hashids::decode($siteHashID)[0]);

        $branch = null;

        switch ($site->userRepositoryProvider->repositoryProvider->provider_name) {
            case OauthController::GITHUB:
            case OauthController::GITLAB:
                $branch = substr($request->get('ref'), strrpos($request->get('ref'), '/') + 1);
                break;
            case OauthController::BITBUCKET:
                $branch = $request->get('push')['changes'][0]['new']['name'];
                break;
        }

        if ($site->branch == $branch) {
            dispatch(
                (new \App\Jobs\Site\DeploySite($site, null, true))->onQueue(env('SERVER_COMMAND_QUEUE'))
            );
        }

        return response()->json('OK');
    }

    public function loadMonitor(Request $request, $serverHasId)
    {
        $server = Server::findOrFail(\Hashids::decode($serverHasId)[0]);

        $server->update([
            'stats->cpus' => $request->get('cpus'),
            'stats->loads' => $this->getStats($request->get('loads')),
        ]);

        $server->notify(new ServerLoad($server));

        return response()->json('OK');
    }

    public function memoryMonitor(Request $request, $serverHasId)
    {
        $server = Server::findOrFail(\Hashids::decode($serverHasId)[0]);

        $memoryStats = $this->getStats($request->get('memory'));

        $server->update([
            'stats->memory->'.$memoryStats['name'] => $memoryStats,
        ]);

        $server->notify(new ServerMemory($server));

        return response()->json('OK');
    }

    public function diskUsageMonitor(Request $request, $serverHasId)
    {
        $server = Server::findOrFail(\Hashids::decode($serverHasId)[0]);

        $diskStats = $this->getStats($request->get('disk_usage'));

        $server->update([
            'stats->disk_usage->'.$diskStats['disk'] => $diskStats,
        ]);

        $server->notify(new ServerDiskUsage($server));

        return response()->json('OK');
    }

    private function getStats($items)
    {
        $stats = [];
        foreach (explode(' ', $items) as $stat) {
            $statParts = explode('=', $stat);
            $stats[$statParts[0]] = $statParts[1];
        }

        return $stats;
    }
}

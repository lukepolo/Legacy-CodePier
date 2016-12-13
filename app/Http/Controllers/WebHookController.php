<?php

namespace App\Http\Controllers;

use App\Models\Server\Server;
use App\Models\Site\Site;
use Illuminate\Http\Request;

class WebHookController extends Controller
{
    /**
     * @param $siteHashID
     * @return \Illuminate\Http\JsonResponse
     */
    public function deploy($siteHashID)
    {
        dispatch(new \App\Jobs\Site\DeploySite(
            Site::findOrFail(\Hashids::decode($siteHashID)[0])
        ));

        return response()->json('OK');
    }

    public function loadMonitor(Request $request, $serverHasId)
    {
        $server = Server::findOrFail(\Hashids::decode($serverHasId)[0]);

        $server->update([
            'stats->loads' => $this->getStats($request->get('loads'))
        ]);

        return response()->json('OK');
    }

    public function memoryMonitor(Request $request, $serverHasId)
    {
        $server = Server::findOrFail(\Hashids::decode($serverHasId)[0]);

        $memoryStats = $this->getStats($request->get('memory'));

        $server->update([
            'stats->memory->'.$memoryStats['name'] => $memoryStats
        ]);

        return response()->json('OK');
    }

    public function diskUsageMonitor(Request $request, $serverHasId)
    {
        $server = Server::findOrFail(\Hashids::decode($serverHasId)[0]);

        $diskStats = $this->getStats($request->get('disk_usage'));

        $server->update([
            'stats->disk_usage->'.$diskStats['disk'] => $diskStats
        ]);

        return response()->json('OK');
    }
    private function getStats($items)
    {
        $stats = [];
        foreach(explode(' ', $items) as $stat) {
            $statParts = explode('=', $stat);
            $stats[$statParts[0]] = $statParts[1];
        }
        return $stats;
    }
}

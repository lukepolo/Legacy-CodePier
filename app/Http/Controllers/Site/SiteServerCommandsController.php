<?php

namespace App\Http\Controllers\Site;

use App\Models\Site\Site;
use App\Models\ServerCommand;
use App\Models\Site\SiteDeployment;
use App\Http\Controllers\Controller;

class SiteServerCommandsController extends Controller
{
    /**
     * @param $siteId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($siteId)
    {
        $site = Site::with('servers')->findOrFail($siteId);

        $serverCommands = ServerCommand::whereIn('server_id', $site->servers->pluck('id'))
            ->where('failed', false)
            ->where('completed', false)
            ->get();

        foreach ($serverCommands as $serverCommand) {
            $serverCommand->update([
                'failed' => true,
            ]);
        }

        $siteDeployments = SiteDeployment::where('site_id', $site->id)
            ->whereNotIn('status', [
                'Failed',
                'Completed',
            ])
            ->get();

        foreach ($siteDeployments as $siteDeployment) {
            $siteDeployment->update([
                'status' => 'Failed',
            ]);
        }

        return response()->json('OK');
    }
}

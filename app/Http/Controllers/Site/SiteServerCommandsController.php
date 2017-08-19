<?php

namespace App\Http\Controllers\Site;

use App\Models\Site\Site;
use App\Models\ServerCommand;
use App\Http\Controllers\Controller;

class SiteServerCommandsController extends Controller
{
    /**
     * @param $siteId
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($siteId)
    {
        $site = Site::with('servers')->findOrFail($siteId);

        $serverCommands = ServerCommand::whereIn('server_id', $site->servers->pluck('id'))
            ->where('started', false)
            ->where('failed', false)
            ->where('completed', false)
            ->get();

        \DB::table('server_commands')->where('id', 18)->delete();

        dd('here');
        foreach ($serverCommands as $serverCommand) {
            dump('Deleting '.$serverCommand->id);
            dd('Results : '.$serverCommand->delete());
        }

        return response()->json('OK');
    }
}

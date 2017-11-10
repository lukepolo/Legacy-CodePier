<?php

namespace App\Http\Controllers\Site;

use App\Models\Site\Site;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SiteNotificationChannelsController extends Controller
{
    /**
     * @param Request $request
     * @param $siteId
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $siteId)
    {
        $site = Site::findOrFail($siteId);

        $site->update([
            'slack_channel_preferences' => $request->get('slack_channel_preferences')
        ]);

        return response()->json($site);
    }
}

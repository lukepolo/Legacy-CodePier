<?php

namespace App\Http\Controllers\Site;

use App\Models\Site\Site;
use App\Http\Controllers\Controller;
use App\Http\Requests\NotificationChannelsRequest;

class SiteNotificationChannelsController extends Controller
{
    /**
     * @param NotificationChannelsRequest $request
     * @param $siteId
     *
     * @return \Illuminate\Http\Response
     */
    public function store(NotificationChannelsRequest $request, $siteId)
    {
        $site = Site::findOrFail($siteId);

        $site->update([
            'slack_channel_preferences' => $request->get('slack_channel_preferences'),
        ]);

        return response()->json($site);
    }
}

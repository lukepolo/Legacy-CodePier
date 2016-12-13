<?php

namespace App\Http\Controllers;

use App\Models\Site\Site;

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
}

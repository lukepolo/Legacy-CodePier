<?php

namespace App\Http\Controllers;

use App\Models\Site\Site;

class WebHookController extends Controller
{
    /**
     * @param $siteHashID
     */
    public function deploy($siteHashID)
    {
        dispatch(new \App\Jobs\Site\DeploySite(
            Site::findOrFail(\Hashids::decode($siteHashID)[0])
        ));
    }
}

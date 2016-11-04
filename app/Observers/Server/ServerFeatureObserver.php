<?php

namespace App\Observers\Server;

use App\Models\Server\ServerFeature;

/**
 * Class ServerFeatureObserver.
 */
class ServerFeatureObserver
{
    /**
     * @param ServerFeature $serverFeature
     */
    public function created(ServerFeature $serverFeature)
    {

    }


    /**
     * @param ServerFeature $serverFeature
     */
    public function deleting(ServerFeature $serverFeature)
    {

    }
}

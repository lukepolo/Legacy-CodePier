<?php

namespace App\Observers\Server;

use App\Models\Site\SiteServerDeployment;

class ServerDeploymentObserver
{
    public function created(SiteServerDeployment $siteServerDeployment)
    {
        $siteServerDeployment->siteDeployment->updateStatus();
    }

    public function updated(SiteServerDeployment $siteServerDeployment)
    {
        $siteServerDeployment->siteDeployment->updateStatus();
    }
}

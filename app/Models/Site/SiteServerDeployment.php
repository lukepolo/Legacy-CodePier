<?php

namespace App\Models\Site;

use App\Models\Server\Server;
use App\Models\Site\Deployment\DeploymentEvent;
use Illuminate\Database\Eloquent\Model;

class SiteServerDeployment extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'log' => 'array',
    ];

    public function server()
    {
        return $this->belongsTo(Server::class);
    }

    public function siteDeployment()
    {
        return $this->belongsTo(SiteDeployment::class);
    }

    public function createSteps()
    {
        foreach ($this->siteDeployment->site->deploymentSteps as $deploymentStep) {
            DeploymentEvent::create([
                'site_server_deployment_id' => $this->id,
                'deployment_step_id' => $deploymentStep->id,
            ]);
        }

        return $this;
    }

    public function events()
    {
        return $this->hasMany(DeploymentEvent::class);
    }

    public function delete()
    {
        $this->events->delete();

        return parent::delete();
    }
}

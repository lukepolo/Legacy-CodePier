<?php

namespace App\Models\Site;

use App\Models\Server\Server;
use App\Traits\ConnectedToUser;
use Illuminate\Database\Eloquent\Model;
use App\Models\Site\Deployment\DeploymentEvent;

class SiteServerDeployment extends Model
{
    use ConnectedToUser;

    protected $guarded = ['id'];

    public static $userModel = 'server';

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

            $skipStep = false;

            if (! empty($deploymentStep->server_types)) {
                $skipStep = !collect($deploymentStep->server_types)->contains($this->server->type);
            } elseif (! empty($deploymentStep->servers)) {
                $skipStep = !collect($deploymentStep->servers)->contains($this->server_id);
            }

            if($skipStep === false) {
                DeploymentEvent::create([
                    'site_server_deployment_id' => $this->id,
                    'deployment_step_id' => $deploymentStep->id,
                ]);
            }
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

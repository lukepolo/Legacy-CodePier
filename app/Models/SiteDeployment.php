<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class SiteDeployment
 * @package App\Models
 */
class SiteDeployment extends Model
{
    protected $guarded = ['id'];

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function createSteps()
    {
        foreach($this->site->deploymentSteps as $deploymentStep) {
            DeploymentEvent::create([
                'site_deployment_id' => $this->id,
                'deployment_step_id' => $deploymentStep->id
            ]);
        }
    }

    public function events()
    {
        return $this->hasMany(DeploymentEvent::class);
    }
}

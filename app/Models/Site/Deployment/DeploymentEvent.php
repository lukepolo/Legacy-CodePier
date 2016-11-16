<?php

namespace App\Models\Site\Deployment;

use App\Models\Site\Site;
use App\Models\Site\SiteServerDeployment;
use Illuminate\Database\Eloquent\Model;

class DeploymentEvent extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'log' => 'array',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function step()
    {
        return $this->belongsTo(DeploymentStep::class, 'deployment_step_id', 'id');
    }

    public function serverDeployment()
    {
        return $this->belongsTo(SiteServerDeployment::class, 'site_server_deployment_id');
    }

}

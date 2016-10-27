<?php

namespace App\Models\Site\Deployment;

use Illuminate\Database\Eloquent\Model;

/**
 * Class DeploymentEvent.
 */
class DeploymentEvent extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'log' => 'array',
    ];

    public function step()
    {
        return $this->belongsTo(DeploymentStep::class, 'deployment_step_id', 'id');
    }
}

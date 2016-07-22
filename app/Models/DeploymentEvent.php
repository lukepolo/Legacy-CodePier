<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class DeploymentEvent
 * @package App\Models
 */
class DeploymentEvent extends Model
{
    protected $guarded = ['id'];

    public function step()
    {
        return $this->belongsTo(DeploymentStep::class, 'deployment_step_id', 'id');
    }
}

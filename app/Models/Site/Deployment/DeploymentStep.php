<?php

namespace App\Models\Site\Deployment;

use App\Models\Site\Site;
use Illuminate\Database\Eloquent\Model;

class DeploymentStep extends Model
{
    protected $guarded = ['id'];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function site()
    {
        return $this->belongsTo(Site::class);
    }
}

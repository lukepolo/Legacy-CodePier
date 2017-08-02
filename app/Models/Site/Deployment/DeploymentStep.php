<?php

namespace App\Models\Site\Deployment;

use App\Models\Site\Site;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DeploymentStep extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'servers' => 'array',
        'server_types' => 'array'
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
}

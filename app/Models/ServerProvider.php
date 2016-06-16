<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServerProvider extends Model
{
    protected $guarded = ['id'];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function serverOptions()
    {
        return $this->hasMany(ServerProviderOption::class);
    }

    public function serverRegions()
    {
        return $this->hasMany(ServerProviderRegion::class);
    }
}

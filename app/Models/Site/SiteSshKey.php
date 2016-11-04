<?php

namespace App\Models\Site;

use App\Models\Server\ServerSshKey;
use Illuminate\Database\Eloquent\Model;

class SiteSshKey extends Model
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

    public function serverSshKeys()
    {
        return $this->hasMany(ServerSshKey::class);
    }
}

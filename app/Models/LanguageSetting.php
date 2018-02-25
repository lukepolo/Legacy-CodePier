<?php

namespace App\Models;

use App\Models\Server\Server;
use App\Models\Site\Site;
use App\Traits\HasServers;
use Illuminate\Database\Eloquent\Model;

class LanguageSetting extends Model
{
    use HasServers;

    protected $guarded = ['id'];

    protected $casts = [
        'params' => 'array',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function sites()
    {
        return $this->morphedByMany(Site::class, 'language_settingable');
    }

    public function servers()
    {
        return $this->morphedByMany(Server::class, 'language_settingable');
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function commandDescription($status)
    {
        return $status.' language setting '.$this->setting.' for '.$this->language;
    }
}

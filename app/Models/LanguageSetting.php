<?php

namespace App\Models;

use App\Models\Site\Site;
use App\Models\Server\Server;
use Illuminate\Database\Eloquent\Model;

class LanguageSetting extends Model
{
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
}

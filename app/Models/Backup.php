<?php

namespace App\Models;

use App\Models\Site\Site;
use App\Models\Server\Server;
use Illuminate\Database\Eloquent\Model;

class Backup extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'items' => 'array',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function sites()
    {
        return $this->morphedByMany(Site::class, 'backupable');
    }

    public function servers()
    {
        return $this->morphedByMany(Server::class, 'backupable');
    }
}

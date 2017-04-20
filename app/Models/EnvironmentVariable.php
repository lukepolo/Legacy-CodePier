<?php

namespace App\Models;

use App\Models\Server\Server;
use App\Models\Site\Site;
use Illuminate\Database\Eloquent\Model;

class EnvironmentVariable extends Model
{
    protected $guarded = ['id'];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function sites()
    {
        return $this->morphedByMany(Site::class, 'environmentVariable');
    }

    public function servers()
    {
        return $this->morphedByMany(Server::class, 'environmentVariable');
    }
}

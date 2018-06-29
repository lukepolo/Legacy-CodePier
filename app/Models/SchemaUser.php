<?php

namespace App\Models;

use App\Models\Site\Site;
use App\Traits\HasServers;
use App\Traits\Encryptable;
use App\Models\Server\Server;
use Illuminate\Database\Eloquent\Model;

class SchemaUser extends Model
{
    use Encryptable, HasServers;

    protected $encryptable = [
        'password',
    ];

    protected $guarded = ['id'];

    protected $casts = [
        'schema_ids' => 'array',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function sites()
    {
        return $this->morphedByMany(Site::class, 'schema_userable');
    }

    public function servers()
    {
        return $this->morphedByMany(Server::class, 'schema_userable');
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function commandDescription($status)
    {
        return $status.' database user '.$this->name;
    }
}

<?php

namespace App\Models;

use App\Models\Server\Server;
use App\Models\Site\Site;
use App\Traits\Encryptable;
use Illuminate\Database\Eloquent\Model;

class SshKey extends Model
{
    use Encryptable;

    protected $guarded = ['id'];

    protected $encryptable = [
        'ssh_key',
    ];

    protected $hidden = [
        'ssh_key',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function sites()
    {
        return $this->morphedByMany(Site::class, 'sshKeyable');
    }

    public function servers()
    {
        return $this->morphedByMany(Server::class, 'sshKeyable');
    }
}

<?php

namespace App\Models\Server;

use App\Traits\ConnectedToUser;
use App\Traits\Encryptable;
use Illuminate\Database\Eloquent\Model;

class ServerSshKey extends Model
{
    use ConnectedToUser, Encryptable;

    protected $guarded = ['id'];

    public static $userModel = 'server';

    protected $encryptable = [
        'ssh_key',
    ];

    public function server()
    {
        return $this->belongsTo(Server::class);
    }
}

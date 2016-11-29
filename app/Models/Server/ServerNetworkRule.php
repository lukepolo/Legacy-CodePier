<?php

namespace App\Models\Server;

use App\Traits\ConnectedToUser;
use Illuminate\Database\Eloquent\Model;

class ServerNetworkRule extends Model
{
    use ConnectedToUser;

    protected $guarded = ['id'];

    static $userModel = 'server';

    public function server()
    {
        return $this->belongsTo(Server::class);
    }
}

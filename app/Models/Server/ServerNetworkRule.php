<?php

namespace App\Models\Server;

use Illuminate\Database\Eloquent\Model;

class ServerNetworkRule extends Model
{
    protected $guarded = ['id'];

    public function server()
    {
        return $this->belongsTo(Server::class);
    }
}

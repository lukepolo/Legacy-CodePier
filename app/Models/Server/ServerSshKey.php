<?php

namespace App\Models\Server;

use Illuminate\Database\Eloquent\Model;

class ServerSshKey extends Model
{
    protected $guarded = ['id'];

    public function server()
    {
        return $this->belongsTo(Server::class);
    }
}

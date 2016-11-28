<?php

namespace App\Models;

use App\Models\Server\Server;
use Illuminate\Database\Eloquent\Model;

class ServerCommand extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'log'  => 'array',
    ];

    public function server()
    {
        return $this->belongsTo(Server::class);
    }
}

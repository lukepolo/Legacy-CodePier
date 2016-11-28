<?php

namespace App\Models\Server;

use Illuminate\Database\Eloquent\Model;

class ServerProvisionStep extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'log' => 'array',
        'parameters' => 'array',
    ];

    public function server()
    {
        return $this->belongsTo(Server::class);
    }
}

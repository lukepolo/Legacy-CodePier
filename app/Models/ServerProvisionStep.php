<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServerProvisionStep extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'log' => 'array',
    ];

    public function server()
    {
        return $this->belongsTo(Server::class);
    }
}

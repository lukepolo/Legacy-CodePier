<?php

namespace App\Models\Server;

use App\Traits\ConnectedToUser;
use Illuminate\Database\Eloquent\Model;

class ServerProvisionStep extends Model
{
    use ConnectedToUser;

    protected $guarded = ['id'];

    static $userModel = 'server';

    protected $casts = [
        'log' => 'array',
        'parameters' => 'array',
    ];

    public function server()
    {
        return $this->belongsTo(Server::class);
    }
}

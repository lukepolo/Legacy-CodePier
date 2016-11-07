<?php

namespace App\Models\Server;

use App\Traits\ServerCommands;
use Illuminate\Database\Eloquent\Model;

class ServerProvisionStep extends Model
{
    use ServerCommands;

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

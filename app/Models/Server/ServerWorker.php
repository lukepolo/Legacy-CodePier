<?php

namespace App\Models\Server;

use App\Traits\ServerCommands;
use Illuminate\Database\Eloquent\Model;

class ServerWorker extends Model
{
    use ServerCommands;

    protected $guarded = ['id'];

    public function server()
    {
        return $this->belongsTo(Server::class)->where('progress', '>=', 100);
    }
}

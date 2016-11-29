<?php

namespace App\Models\Server;

use App\Traits\ConnectedToUser;
use Illuminate\Database\Eloquent\Model;

class ServerWorker extends Model
{
    use ConnectedToUser;

    protected $guarded = ['id'];

    public static $userModel = 'server';

    public function server()
    {
        return $this->belongsTo(Server::class)->where('progress', '>=', 100);
    }
}

<?php

namespace App\Models\Server;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ServerWorker.
 */
class ServerWorker extends Model
{
    protected $guarded = ['id'];

    public function server()
    {
        return $this->belongsTo(Server::class)->where('progress', '>=', 100);
    }
}

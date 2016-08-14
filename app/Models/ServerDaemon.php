<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ServerDaemon
 * @package App\Models
 */
class ServerDaemon extends Model
{
    protected $guarded = ['id'];

    public function server()
    {
        return $this->belongsTo(Server::class);
    }
}

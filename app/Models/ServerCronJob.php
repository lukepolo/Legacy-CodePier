<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ServerCronJob.
 */
class ServerCronJob extends Model
{
    protected $guarded = ['id'];

    public function server()
    {
        return $this->belongsTo(Server::class);
    }
}

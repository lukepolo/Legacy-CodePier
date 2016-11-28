<?php

namespace App\Models\Server;

use Illuminate\Database\Eloquent\Model;

class ServerCronJob extends Model
{
    protected $guarded = ['id'];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function server()
    {
        return $this->belongsTo(Server::class);
    }
}

<?php

namespace App\Models\Server;

use App\Traits\ConnectedToUser;
use Illuminate\Database\Eloquent\Model;

class ServerCronJob extends Model
{
    use ConnectedToUser;

    protected $guarded = ['id'];

    static $userModel = 'server';

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

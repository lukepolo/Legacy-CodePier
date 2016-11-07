<?php

namespace App\Models\Server;

use App\Traits\ServerCommands;
use Illuminate\Database\Eloquent\Model;

class ServerCronJob extends Model
{
    use ServerCommands;

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

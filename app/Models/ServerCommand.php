<?php

namespace App\Models;

use App\Models\Server\Server;
use App\Traits\ConnectedToUser;
use Illuminate\Database\Eloquent\Model;

class ServerCommand extends Model
{
    //    use ConnectedToUser;

    public static $userModel = 'server';

    protected $guarded = ['id'];

    protected $casts = [
        'log'  => 'array',
    ];

    public function server()
    {
        return $this->belongsTo(Server::class);
    }

    public function command()
    {
        return $this->belongsTo(Command::class);
    }

    //    public function delete()
//    {
//        $this->command()->delete();
//        return parent::delete();
//    }
}

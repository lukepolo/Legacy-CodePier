<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ServerSshKey
 * @package App\Models
 */
class ServerSshKey extends Model
{
    protected $guarded = ['id'];

    public function server()
    {
        return $this->belongsTo(Server::class);
    }
}

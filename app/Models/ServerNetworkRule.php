<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ServerNetworkRule
 * @package App\Models
 */
class ServerNetworkRule extends Model
{
    protected $guarded = ['id'];

    public function server()
    {
        return $this->belongsTo(Server::class);
    }
}

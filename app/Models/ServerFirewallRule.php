<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ServerFirewallRule
 * @package App\Models
 */
class ServerFirewallRule extends Model
{
    protected $guarded = ['id'];

    public function server()
    {
        return $this->belongsTo(Server::class);
    }
}

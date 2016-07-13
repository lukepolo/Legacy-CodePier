<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Server extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'options' => 'array',
        'features' => 'array',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function serverProvider()
    {
        return $this->belongsTo(ServerProvider::class);
    }

    public function sites()
    {
        return $this->hasMany(Site::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sshKeys()
    {
        return $this->hasMany(ServerSshKey::class);
    }

    public function cronJobs()
    {
        return $this->hasMany(ServerCronJob::class);
    }

    public function firewallRules()
    {
        return $this->hasMany(ServerFirewallRule::class);
    }

    public function daemons()
    {
        return $this->hasMany(ServerDaemon::class);
    }
}

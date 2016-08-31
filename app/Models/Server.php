<?php

namespace App\Models;

use App\Traits\UsedByTeams;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

/**
 * Class Server
 * @package App\Models
 */
class Server extends Model
{
    use SoftDeletes, UsedByTeams, Notifiable;

    protected $guarded = ['id'];

    static $teamworkModel = 'pile.teams';
    public $teamworkSync = false;

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
        return $this->belongsToMany(Site::class);
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

    public function connectedServers()
    {
        return $this->hasMany(ServerNetworkRule::class);
    }

    public function hasFeature($feature)
    {
        return $this->getFeatures()->pluck('option')->contains($feature);
    }

    public function features()
    {
        return $this->hasMany(ServerProviderFeatures::class);
    }

    public function pile()
    {
        return $this->belongsTo(Pile::class);
    }

    public function provisionSteps()
    {
        return $this->hasMany(ServerProvisionStep::class);
    }
    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function getFeatures()
    {
        $features = [];

        foreach ($this->features as $featureID) {
            $features[] = ServerProviderFeatures::findOrFail((int) $featureID);
        }

        return collect($features);
    }

    public function encode()
    {
        return \Hashids::encode($this->id);
    }

    public function decode($hash)
    {
        return $this->findOrFail(\Hashids::decode($hash));
    }

    /**
     * Route notifications for the mail channel.
     *
     * @return string
     */
    public function routeNotificationForMail()
    {
        $emails = collect($this->user->email);

        $this->load('pile.teams.users');

        foreach($this->pile->teams as $team) {
            $emails->merge($team->users->pluck('email'));
        }

        return $emails->toArray();
    }
}

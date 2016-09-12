<?php

namespace App\Models;

use App\Traits\Encryptable;
use App\Traits\UsedByTeams;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

/**
 * Class Server.
 */
class Server extends Model
{
    use SoftDeletes, UsedByTeams, Notifiable, Encryptable;

    protected $guarded = ['id'];

    public static $teamworkModel = 'pile.teams';
    public $teamworkSync = false;

    protected $casts = [
        'options'  => 'array',
        'server_provider_features' => 'array',
        'server_features' => 'array',
    ];

    protected $encryptable = [
        'sudo_password',
        'public_ssh_key',
        'private_ssh_key',
        'database_password',
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

    public function workers()
    {
        return $this->hasMany(ServerWorker::class);
    }

    public function connectedServers()
    {
        return $this->hasMany(ServerNetworkRule::class);
    }

    public function server_provider_features()
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

    public function hasServerProviderFeature($feature)
    {
        return $this->getServerProviderFeatures()->pluck('option')->contains($feature);
    }

    public function getServerProviderFeatures()
    {
        $features = [];

        foreach ($this->server_provider_features as $featureID) {
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

        foreach ($this->pile->teams as $team) {
            $emails->merge($team->users->pluck('email'));
        }

        return $emails->toArray();
    }
}

<?php

namespace App\Models\Site;

use App\Http\Controllers\Auth\OauthController;
use App\Models\Command;
use App\Models\Pile;
use App\Models\Server\Server;
use App\Models\Site\Deployment\DeploymentStep;
use App\Models\User\User;
use App\Models\User\UserRepositoryProvider;
use App\Traits\ConnectedToUser;
use App\Traits\FireEvents;
use App\Traits\UsedByTeams;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Site extends Model
{
    use UsedByTeams, Notifiable, FireEvents, SoftDeletes, ConnectedToUser;

    protected $guarded = [
        'id',
    ];

    public static $teamworkModel = 'pile.teams';

    public $teamworkSync = false;

    protected $appends = ['path'];

    protected $casts = [
        'server_features' => 'array',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function activeSSL()
    {
        return $this->hasOne(SiteSslCertificate::class)->where('active', true);
    }

    public function cronJobs()
    {
        return $this->hasMany(SiteCronJob::class);
    }

    public function deployments()
    {
        return $this->hasMany(SiteDeployment::class)->orderBy('id', 'desc');
    }

    public function deploymentSteps()
    {
        return $this->hasMany(DeploymentStep::class)->orderBy('order');
    }

    public function files()
    {
        return $this->hasMany(SiteFile::class);
    }

    public function firewallRules()
    {
        return $this->hasMany(SiteFirewallRule::class);
    }

    public function pile()
    {
        return $this->belongsTo(Pile::class);
    }

    public function provisionedServers()
    {
        return $this->belongsToMany(Server::class)->where('progress', '>=', '100');
    }

    public function servers()
    {
        return $this->belongsToMany(Server::class);
    }

    public function commands()
    {
        return $this->hasMany(Command::class);
    }

    public function ssls()
    {
        return $this->hasMany(SiteSslCertificate::class)->orderBy('id', 'desc');
    }

    public function sshKeys()
    {
        return $this->hasMany(SiteSshKey::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function userRepositoryProvider()
    {
        return $this->belongsTo(UserRepositoryProvider::class);
    }

    public function workers()
    {
        return $this->hasMany(SiteWorker::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function decode($hash)
    {
        return $this->findOrFail(\Hashids::decode($hash));
    }

    public function hasActiveSSL()
    {
        if (! empty($this->activeSSL)) {
            return true;
        }

        return false;
    }

    public function lastDeployment()
    {
        return $this->hasOne(SiteDeployment::class)->orderBy('id', 'desc');
    }

    public function encode()
    {
        return \Hashids::encode($this->id);
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

    /**
     * Route notifications for the Slack channel.
     *
     * @return string
     */
    public function routeNotificationForSlack()
    {
        $slackProvider = $this->user->userNotificationProviders->first(function ($userNotificationProvider) {
            return $userNotificationProvider->notificationProvider->provider_name == OauthController::SLACK;
        });

        return $slackProvider ? $slackProvider->token : null;
    }

    public function getSlackChannel()
    {
        return 'general';
    }

    /**
     * Get the administrator flag for the user.
     *
     * @return bool
     */
    public function getPathAttribute()
    {
        return '/home/codepier/'.$this->domain;
    }

    /**
     * Gets the sites language that its using.
     *
     * @return string
     */
    public function getSiteLanguage()
    {
        return 'PHP\\PHP';

        $language = collect($this->server_features)->filter(function ($features, $index) {
            return starts_with($index, 'Language');
        })->keys()->first();

        return substr($language, strpos($language, '\\') + 1);
    }

    public function getFrameworkClass()
    {
        return str_replace('.', '\\Frameworks\\', $this->framework);
    }
}

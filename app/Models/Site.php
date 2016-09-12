<?php

namespace App\Models;

use App\Http\Controllers\Auth\OauthController;
use App\Traits\UsedByTeams;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

/**
 * Class Site.
 */
class Site extends Model
{
    use UsedByTeams, Notifiable;

    protected $guarded = ['id'];

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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function servers()
    {
        return $this->belongsToMany(Server::class);
    }

    public function pile()
    {
        return $this->belongsTo(Pile::class);
    }

    public function activeSSL()
    {
        return $this->hasOne(SiteSslCertificate::class)->where('active', true);
    }

    public function ssls()
    {
        return $this->hasMany(SiteSslCertificate::class)->orderBy('id', 'desc');
    }

    public function workers()
    {
        return $this->hasMany(SiteWorker::class);
    }

    public function deploymentSteps()
    {
        return $this->hasMany(DeploymentStep::class)->orderBy('order');
    }

    public function deployments()
    {
        return $this->hasMany(SiteDeployment::class)->orderBy('id', 'desc');
    }

    public function userRepositoryProvider()
    {
        return $this->belongsTo(UserRepositoryProvider::class);
    }

    public function files()
    {
        return $this->hasMany(SiteFile::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

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

    public function fireSavedEvent()
    {
        $this->fireModelEvent('saved', false);
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
}

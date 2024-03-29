<?php

namespace App\Models\Site;

use App\Models\Buoy;
use App\Models\File;
use App\Models\Pile;
use App\Models\Backup;
use App\Models\Daemon;
use App\Models\Schema;
use App\Models\SshKey;
use App\Models\Worker;
use App\Models\Command;
use App\Models\CronJob;
use App\Models\User\User;
use App\Models\SchemaUser;
use App\Traits\HasServers;
use App\Traits\Encryptable;
use App\Traits\UsedByTeams;
use App\Models\FirewallRule;
use App\Models\SlackChannel;
use App\Models\Server\Server;
use App\Models\SslCertificate;
use App\Models\LanguageSetting;
use App\Traits\ConnectedToUser;
use App\Models\EnvironmentVariable;
use App\Services\Server\ServerService;
use App\Services\Systems\SystemService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use App\Models\User\UserRepositoryProvider;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Http\Controllers\Auth\OauthController;
use App\Models\Site\Deployment\DeploymentStep;
use App\Http\Controllers\Auth\Providers\NotificationProvidersController;

class Site extends Model
{
    use UsedByTeams, Notifiable, SoftDeletes, ConnectedToUser, Encryptable, HasServers;

    const STARTING_PORT = 8080;

    protected $guarded = [
        'id',
        'public_ssh_key',
        'private_ssh_key',
    ];

    protected $encryptable = [
        'private_ssh_key',
    ];

    protected $hidden = [
        'private_ssh_key',
        'server_features',
    ];

    public static $teamworkModel = 'pile.teams';

    public $teamworkSync = false;

    protected $appends = [
        'path',
        'last_deployment_status',
    ];

    protected $casts = [
        'workflow' => 'array',
        'server_features' => 'array',
        'slack_channel_preferences' => 'array',
    ];

    protected $with = [
        'lastDeployment',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function activeSsl()
    {
        return $this->sslCertificates
            ->where('active', true)
            ->where('key', '!=', null)
            ->where('cert', '!=', null)
            ->first();
    }

    public function letsEncryptSslCertificates()
    {
        return $this->sslCertificates->where('type', ServerService::LETS_ENCRYPT);
    }

    public function cronJobs()
    {
        return $this->morphToMany(CronJob::class, 'cronJobable');
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
        return $this->morphToMany(File::class, 'fileable');
    }

    public function firewallRules()
    {
        return $this->morphToMany(FirewallRule::class, 'firewallRuleable');
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

    public function sslCertificates()
    {
        return $this->morphToMany(SslCertificate::class, 'sslCertificateable');
    }

    public function sshKeys()
    {
        return $this->morphToMany(SshKey::class, 'sshKeyable');
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
        return $this->morphToMany(Worker::class, 'workerable');
    }

    public function daemons()
    {
        return $this->morphToMany(Daemon::class, 'daemonable');
    }

    public function buoys()
    {
        return $this->morphToMany(Buoy::class, 'buoyable');
    }

    public function schemas()
    {
        return $this->morphToMany(Schema::class, 'schemable');
    }

    public function schemaUsers()
    {
        return $this->morphToMany(SchemaUser::class, 'schema_userable');
    }

    public function environmentVariables()
    {
        return $this->morphToMany(EnvironmentVariable::class, 'environmentable');
    }

    public function languageSettings()
    {
        return $this->morphToMany(LanguageSetting::class, 'language_settingable');
    }

    public function lifelines()
    {
        return $this->hasMany(Lifeline::class);
    }

    public function backups()
    {
        return $this->morphToMany(Backup::class, 'backupable');
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function hasActiveSSL()
    {
        if (! empty($this->activeSsl())) {
            return true;
        }

        return false;
    }

    public function lastDeployment()
    {
        return $this->hasOne(SiteDeployment::class)->orderBy('id', 'desc');
    }


    public function runningDeployment()
    {
        return $this->hasOne(SiteDeployment::class)->where('status', SiteDeployment::RUNNING)->orderBy('id', 'desc');
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

    /**
     * Route notifications for the Discord channel.
     *
     * @return string
     */
    public function routeNotificationForDiscord()
    {
        $discordProvider = $this->user->userNotificationProviders->first(function ($userNotificationProvider) {
            return $userNotificationProvider->notificationProvider->provider_name == NotificationProvidersController::DISCORD;
        });

        return $discordProvider ? $discordProvider->token : null;
    }

    public function slackChannel()
    {
        return $this->morphOne(SlackChannel::class, 'slackable');
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

    public function getLastDeploymentStatusAttribute()
    {
        if ($this->lastDeployment) {
            return $this->lastDeployment->status;
        }
    }

    /**
     * Gets the sites language that its using.
     *
     * @return string
     */
    public function getSiteLanguage()
    {
        return $this->type.'\\'.$this->type;
    }

    public function getFrameworkClass()
    {
        if ($this->framework) {
            return str_replace('.', '\\Frameworks\\', $this->framework);
        }
    }

    public function getDatabases()
    {
        if (isset($this->server_features[SystemService::DATABASE])) {
            return collect($this->server_features[SystemService::DATABASE])->keys();
        }
    }

    public function getWorkers()
    {
        if (isset($this->server_features[SystemService::WORKERS])) {
            return collect($this->server_features[SystemService::WORKERS])->keys();
        }

        return collect();
    }

    public function getSlackChannelName($area)
    {
        $channelPreferences = $this->slack_channel_preferences;
        if (! empty($channelPreferences) && isset($channelPreferences[$area])) {
            return $channelPreferences[$area];
        }

        return $this->name;
    }

    public function isValidRepository()
    {
        return str_contains($this->repository, '/');
    }
}

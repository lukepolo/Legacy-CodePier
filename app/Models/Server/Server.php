<?php

namespace App\Models\Server;

use App\Models\Buoy;
use App\Models\File;
use App\Models\Backup;
use App\Models\Daemon;
use App\Models\Schema;
use App\Models\SshKey;
use App\Models\Worker;
use App\Models\CronJob;
use App\Traits\Hashable;
use App\Models\Site\Site;
use App\Models\User\User;
use App\Models\SchemaUser;
use App\Traits\Encryptable;
use App\Models\FirewallRule;
use App\Models\SlackChannel;
use App\Models\ServerCommand;
use App\Models\SslCertificate;
use App\Models\LanguageSetting;
use App\Traits\ConnectedToUser;
use App\Models\EnvironmentVariable;
use App\Services\Systems\SystemService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Http\Controllers\Auth\OauthController;
use App\Models\Server\Provider\ServerProvider;
use App\Models\Server\Provider\ServerProviderFeatures;
use App\Http\Controllers\Auth\Providers\NotificationProvidersController;

class Server extends Model
{
    use SoftDeletes, Notifiable, Encryptable, ConnectedToUser, Hashable;

    protected $guarded = [
        'id',
        'sudo_password',
        'public_ssh_key',
        'private_ssh_key',
        'database_password',
    ];

    // TODO - we will need to tie these to teams instead , don't need to make it more complicated
//    public static $teamworkModel = 'site.teams';

    public $teamworkSync = false;

    protected $casts = [
        'stats' => 'array',
        'options'  => 'array',
        'private_ips' => 'array',
        'server_features' => 'array',
        'server_provider_features' => 'array',
        'slack_channel_preferences' => 'array',
    ];

    protected $encryptable = [
        'sudo_password',
        'public_ssh_key',
        'private_ssh_key',
        'database_password',
    ];

    protected $hidden = [
        'options',
        'sudo_password',
        'public_ssh_key',
        'private_ssh_key',
        'database_password',
    ];

    protected $appends = [
        'ssh_key'
    ];

    public function getSshKeyAttribute()
    {
        return $this->public_ssh_key;
    }

    public function getServerFeaturesAttribute()
    {
        $serverFeatures = json_decode($this->attributes['server_features'], true);

        $serverFeatures['NodeService'] = collect($serverFeatures['NodeService'])->sortBy(function ($options, $feature) {
            return $feature === 'NodeJs' ? 0 : 1;
        })->all();

        $serverFeatures['Languages\PHP\PHP'] = collect($serverFeatures['Languages\PHP\PHP'])->sortBy(function ($options, $feature) {
            return $feature === 'PHP' ? 0 : 1;
        })->all();

        return $serverFeatures;
    }

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
        return $this->morphToMany(SshKey::class, 'sshKeyable');
    }

    public function cronJobs()
    {
        return $this->morphToMany(CronJob::class, 'cronJobable');
    }

    public function files()
    {
        return $this->morphToMany(File::class, 'fileable');
    }

    public function firewallRules()
    {
        return $this->morphToMany(FirewallRule::class, 'firewallRuleable');
    }

    public function workers()
    {
        return $this->morphToMany(Worker::class, 'workerable');
    }

    public function daemons()
    {
        return $this->morphToMany(Daemon::class, 'daemonable');
    }

    public function sslCertificates()
    {
        return $this->morphToMany(SslCertificate::class, 'sslCertificateable');
    }

    public function activeSslCertificates()
    {
        return $this->morphToMany(SslCertificate::class, 'sslCertificateable')->where('active', true);
    }

    public function server_provider_features()
    {
        return $this->hasMany(ServerProviderFeatures::class);
    }

    public function provisionSteps()
    {
        return $this->hasMany(ServerProvisionStep::class);
    }

    public function commands()
    {
        return $this->hasMany(ServerCommand::class);
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

    public function backups()
    {
        return $this->morphToMany(Backup::class, 'backupable');
    }

    public function stats() {
        return $this->hasOne(ServerStat::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function generateSudoPassword()
    {
        $this->sudo_password = str_random(32);
    }

    public function generateDatabasePassword()
    {
        $this->database_password = str_random(32);
    }


    public function hasServerProviderFeature($feature)
    {
        return $this->getServerProviderFeatures()->pluck('option')->contains($feature);
    }

    public function getServerProviderFeatures()
    {
        $features = [];

        if ($this->server_provider_features) {
            foreach ($this->server_provider_features as $featureID) {
                $features[] = ServerProviderFeatures::findOrFail((int) $featureID);
            }
        }

        return collect($features);
    }

    public function getLanguages()
    {
        $hasLanguages = [];

        foreach (SystemService::LANGUAGES as $language => $languageClass) {
            if (isset($this->server_features[$languageClass])) {
                if (isset($this->server_features[$languageClass][$language]['enabled'])) {
                    $hasLanguages[$language] = [
                        'class' => $languageClass,
                        'version' => $this->server_features[$languageClass][$language]['parameters']['version'],
                    ];
                }
            }
        }

        return collect($hasLanguages);
    }

    /**
     * Route notifications for the mail channel.
     *
     * @return string
     */
    public function routeNotificationForMail()
    {
        $emails = collect($this->user->email);

        return $emails->toArray();
    }

    public function currentProvisioningStep()
    {
        return $this->provisionSteps->first(function ($step) {
            return ! $step->completed || $step->failed;
        });
    }

    public function provisioningProgress()
    {
        $totalDone = $this->provisionSteps->filter(function ($provisionStep) {
            return $provisionStep->completed;
        })->count();

        if ($totalDone == 0) {
            $totalDone = 1;
        }

        return floor(($totalDone / $this->provisionSteps->count()) * 100);
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

    public function detachDeploymentSteps()
    {
        $this->load('sites.deploymentSteps');
        /** @var Site $site */
        foreach ($this->sites as $site) {
            foreach ($site->deploymentSteps as $deploymentStep) {
                $deploymentStep->update([
                    'server_ids' => collect($deploymentStep->server_ids)->filter(function ($serverId) {
                        return $serverId != $this->id;
                    })
                ]);
            }
        }
    }
}

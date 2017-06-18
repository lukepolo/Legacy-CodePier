<?php

namespace App\Models\User;

use App\Models\Pile;
use App\Models\SshKey;
use App\Models\Server\Server;
use Laravel\Cashier\Billable;
use Laravel\Passport\HasApiTokens;
use App\Models\Site\SiteDeployment;
use Illuminate\Notifications\Notifiable;
use Mpociot\Teamwork\Traits\UserHasTeams;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    const USER = 'user';
    const ADMIN = 'admin';

    use Notifiable, UserHasTeams, Billable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'workflow',
        'current_pile_id',
        'invited_to_slack',
        'second_auth_active',
        'second_auth_updated_at',
        'user_login_provider_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'second_auth_updated_at',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function servers()
    {
        return $this->hasMany(Server::class);
    }

    public function provisionedServers()
    {
        return $this->hasMany(Server::class)->where('progress', '>=', '100');
    }

    public function userServerProviders()
    {
        return $this->hasMany(UserServerProvider::class);
    }

    public function userLoginProviders()
    {
        return $this->hasMany(UserLoginProvider::class);
    }

    public function userRepositoryProviders()
    {
        return $this->hasMany(UserRepositoryProvider::class);
    }

    public function userNotificationProviders()
    {
        return $this->hasMany(UserNotificationProvider::class);
    }

    public function sshKeys()
    {
        return $this->morphToMany(SshKey::class, 'sshKeyable');
    }

    public function piles()
    {
        return $this->hasMany(Pile::class);
    }

    public function currentPile()
    {
        return $this->belongsTo(Pile::class, 'current_pile_id');
    }

    public function notificationSettings()
    {
        return $this->hasMany(UserNotificationSetting::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function hasRole($role)
    {
        return $this->role == $role;
    }

    public function getRunningCommands()
    {
        $commandsRunning = [];

        if ($this->currentPile) {
            $sites = $this->currentPile
                ->sites()
                ->with(['servers.commands' =>function ($query) {
                    $query->where('failed', 0)
                        ->where('completed', 0);
                }])
                ->get();

            foreach ($sites as $site) {
                foreach ($site->servers as $server) {
                    foreach ($server->commands as $command) {
                        if ($command->command) {
                            $commandsRunning[$command->command->commandable_type][] = $command->command;
                        }
                    }
                }
            }
        }

        return collect($commandsRunning);
    }

    public function getRunningDeployments()
    {
        $deploymentsRunning = [];

        if ($this->currentPile) {
            $sites = $this->currentPile
                ->sites()
                ->whereHas('lastDeployment', function ($query) {
                    $query->whereIn('status', [
                        SiteDeployment::RUNNING,
                        SiteDeployment::QUEUED_FOR_DEPLOYMENT,
                    ]);
                })
                ->get();

            foreach ($sites as $site) {
                $deploymentsRunning[$site->id][] = $site->lastDeployment;
            }
        }

        return collect($deploymentsRunning);
    }
}

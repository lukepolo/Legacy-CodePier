<?php

namespace App\Models\User;

use App\Models\Pile;
use App\Models\Server\Server;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;
use Laravel\Passport\HasApiTokens;
use Mpociot\Teamwork\Traits\UserHasTeams;

class User extends Authenticatable
{
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
        'current_pile_id',
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

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

//    public function servers()
//    {
//        return $this->hasMany(Server::class);
//    }

//    public function provisionedServers()
//    {
//        return $this->hasMany(Server::class)->where('progress', '>=', '100');
//    }

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
        return $this->hasMany(UserSshKey::class);
    }

    public function piles()
    {
        return $this->hasMany(Pile::class);
    }

    public function currentPile()
    {
        return $this->belongsTo(Pile::class, 'current_pile_id');
    }

    public function getRunningCommands()
    {
        $commandsRunning = [];

        if ($this->currentPile) {
            $currentPile = $this->currentPile->with('servers.commands.commandable')->whereHas('servers.commands', function ($query) {
                $query->where('failed', 0)
                    ->where('completed', 0);
            })->first();


            if (! empty($currentPile->servers)) {
                foreach ($currentPile->servers as $server) {
                    foreach ($server->commands as $command) {
                        $commandsRunning[$command->type][] = $command;
                    }
                }
            }
        }

        return collect($commandsRunning);
    }
}

<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Class User
 * @package App\Models
 */
class User extends Authenticatable
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
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

    public function userServerProviders()
    {
        return $this->hasMany(UserServerProvider::class);
    }

    // TODO - change to login providers
    public function userProviders()
    {
        return $this->hasMany(UserProvider::class);
    }

    public function userRepositoryProviders()
    {
        return $this->hasMany(UserRepositoryProvider::class);
    }
}

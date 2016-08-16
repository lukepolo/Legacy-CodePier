<?php

namespace App\Models;

use App\Traits\UsedByTeams;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Site
 * @package App\Models
 */
class Site extends Model
{
    use UsedByTeams;

    static $teamworkUserModel = 'server';

    protected $guarded = ['id'];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function server()
    {
        return $this->belongsToMany(Server::class);
    }

    public function pile()
    {
        return $this->belongsTo(Site::class);
    }

    public function activeSSL()
    {
        return $this->hasOne(SiteSslCertificate::class)->where('active', true);
    }

    public function ssls()
    {
        return $this->hasMany(SiteSslCertificate::class)->orderBy('id', 'desc');
    }

    public function daemons()
    {
        return $this->hasMany(SiteDaemon::class);
    }


    public function settings()
    {
        return $this->hasOne(SiteSettings::class);
    }

    public function deploymentSteps()
    {
        return $this->hasMany(DeploymentStep::class)->orderBy('order');
    }

    public function deployments()
    {
        return $this->hasMany(SiteDeployment::class)->orderBy('id' ,'desc');
    }

    public function userRepositoryProvider()
    {
        return $this->belongsTo(UserRepositoryProvider::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function hasActiveSSL()
    {
        if(!empty($this->activeSSL)) {
            return true;
        }

        return false;
    }

    public function lastDeployment()
    {
        return $this->hasOne(SiteDeployment::class)->orderBy('id' ,'desc');
    }

    public function encode()
    {
        return \Hashids::encode($this->id);
    }

    public function decode($hash)
    {
        return $this->findOrFail(\Hashids::decode($hash));
    }
}

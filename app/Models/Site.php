<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Site
 * @package App\Models
 */
class Site extends Model
{
    protected $guarded = ['id'];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function server()
    {
        return $this->belongsTo(Server::class);
    }

    public function ssl()
    {
        return $this->hasOne(SiteSslCertificate::class);
    }

    public function daemons()
    {
        return $this->hasMany(SiteDaemon::class);
    }

    public function hasSSL()
    {
        if(!empty($this->ssl)) {
            return true;
        }

        return false;
    }

    public function settings()
    {
        return $this->hasOne(SiteSettings::class);
    }

    public function deploymentSteps()
    {
        return $this->hasMany(DeploymentStep::class)->orderBy('order');
    }

    public function lastDeployment()
    {
        return $this->hasOne(SiteDeployment::class)->orderBy('id');
    }

}

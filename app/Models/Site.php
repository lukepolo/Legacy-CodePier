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

    public function hasSSL()
    {
        if(!empty($this->ssl)) {
            return true;
        }

        return false;
    }
}

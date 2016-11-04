<?php

namespace App\Models\Site;

use App\Models\Server\ServerSslCertificate;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SiteSslCertificate.
 */
class SiteSslCertificate extends Model
{
    protected $guarded = ['id'];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function serverSslCertificates()
    {
        return $this->hasMany(ServerSslCertificate::class);
    }
}

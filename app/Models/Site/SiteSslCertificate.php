<?php

namespace App\Models\Site;

use App\Models\Server\ServerSslCertificate;
use App\Traits\FireEvents;
use Illuminate\Database\Eloquent\Model;

class SiteSslCertificate extends Model
{
    use FireEvents;

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

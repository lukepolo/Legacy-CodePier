<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class SiteSslCertificate.
 */
class SiteSslCertificate extends Model
{
    protected $guarded = ['id'];

    public function site()
    {
        return $this->belongsTo(Site::class);
    }
}

<?php

namespace App\Models\Site;

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

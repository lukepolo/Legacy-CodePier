<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class SiteDaemon.
 */
class SiteDaemon extends Model
{
    protected $guarded = ['id'];

    public function site()
    {
        return $this->belongsTo(Site::class);
    }
}

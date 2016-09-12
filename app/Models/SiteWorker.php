<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class SiteWorker.
 */
class SiteWorker extends Model
{
    protected $guarded = ['id'];

    public function site()
    {
        return $this->belongsTo(Site::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class SiteSettings.
 */
class SiteSettings extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'data' => 'array',
    ];
}

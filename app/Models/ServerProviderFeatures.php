<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class ServerProviderFeatures
 * @package App\Models
 */
class ServerProviderFeatures extends Model
{
    use SoftDeletes;
    protected $guarded = ['id'];
}

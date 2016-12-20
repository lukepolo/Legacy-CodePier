<?php

namespace App\Models\Server\Provider;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServerProviderFeatures extends Model
{
    use SoftDeletes;
    protected $guarded = ['id'];
}

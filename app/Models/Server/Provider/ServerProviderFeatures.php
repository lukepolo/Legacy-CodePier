<?php

namespace App\Models\Server\Provider;

use App\Traits\ServerCommands;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServerProviderFeatures extends Model
{
    use SoftDeletes, ServerCommands;
    protected $guarded = ['id'];
}

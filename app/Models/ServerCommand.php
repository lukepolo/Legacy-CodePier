<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServerCommand extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'log'  => 'array',
    ];
}

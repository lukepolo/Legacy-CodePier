<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BuoyApp extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'options' => 'array',
    ];
}

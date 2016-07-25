<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Event
 * @package App\Models
 */
class Event extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'log' => 'array',
    ];
}

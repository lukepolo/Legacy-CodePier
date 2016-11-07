<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Command extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'log'  => 'array',
    ];

    /**
     * Get all of the owning commentable models.
     */
    public function commandable()
    {
        return $this->morphTo();
    }
}

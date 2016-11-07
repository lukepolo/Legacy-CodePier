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
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function commandable()
    {
        return $this->hasOne(Commandable::class);
    }

}

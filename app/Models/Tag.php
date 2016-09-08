<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $guarded = ['id'];

    public function bitts()
    {
        return $this->morphedByMany(Bitt::class, 'taggable');
    }
}

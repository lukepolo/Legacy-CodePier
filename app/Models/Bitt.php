<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bitt extends Model
{
    protected $guarded = ['id'];

    public function tags()
    {
        return $this->morphToMany(Tag::class, 'tagabble');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bitt extends Model
{
    protected $guarded = ['id'];

    public function categories()
    {
        return $this->morphToMany(Category::class, 'categorable');
    }
}

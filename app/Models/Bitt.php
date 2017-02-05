<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bitt extends Model
{
    protected $guarded = ['id'];

    protected $with = [
        'systems',
        'categories',
    ];

    public function categories()
    {
        return $this->morphToMany(Category::class, 'categorable');
    }

    public function systems()
    {
        return $this->belongsToMany(System::class);
    }
}

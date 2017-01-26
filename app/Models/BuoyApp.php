<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BuoyApp extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'ports' => 'array',
        'options' => 'array',
    ];

    protected $appends = [
        'icon_url',
    ];

    protected $with = [
        'categories',
    ];

    public function getIconUrlAttribute()
    {
        if ($this->icon) {
            return \Storage::disk()->url($this->icon);
        }
    }

    public function categories()
    {
        return $this->morphToMany(Category::class, 'categorable');
    }
}

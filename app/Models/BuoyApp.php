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
        'icon_url'
    ];

    public function getIconUrlAttribute()
    {
        return \Storage::disk()->url($this->icon);
    }

}

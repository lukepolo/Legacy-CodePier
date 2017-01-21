<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Buoy extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'options' => 'array',
    ];

    public function buoyApp()
    {
        return $this->belongsTo(BuoyApp::class);
    }
}

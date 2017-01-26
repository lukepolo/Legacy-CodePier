<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Buoy extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'ports' => 'array',
        'options' => 'array',
        'container_ids' => 'array',
    ];

    protected $with = [
        'buoyApp',
    ];

    public function buoyApp()
    {
        return $this->belongsTo(BuoyApp::class);
    }
}

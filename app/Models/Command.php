<?php

namespace App\Models;

use App\Models\Site\Site;
use Illuminate\Database\Eloquent\Model;

class Command extends Model
{
    protected $guarded = ['id'];

    protected $appends = [
        'type',
    ];

    public function getTypeAttribute()
    {
        return get_class($this);
    }

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function serverCommands()
    {
        return $this->hasMany(ServerCommand::class);
    }
}

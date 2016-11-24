<?php

namespace App\Models;

use App\Models\Server\Server;
use App\Models\Site\Site;
use Illuminate\Database\Eloquent\Model;

class Command extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'log'  => 'array',
    ];

    protected $appends = [
        'type',
    ];

    /**
     * Get all of the owning commentable models.
     */
    public function commandable()
    {
        return $this->morphTo();
    }

    public function getTypeAttribute()
    {
        return get_class($this);
    }

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function server()
    {
        return $this->belongsTo(Server::class);
    }
}

<?php

namespace App\Models\Site;

use App\Traits\Hashable;
use Illuminate\Database\Eloquent\Model;

class Lifeline extends Model
{
    use Hashable;

    protected $guarded = ['id'];


    protected $appends = [
        'url',
    ];

    protected $dates = [
        'last_seen',
        'created_at',
        'updated_at',
    ];

    public function site()
    {
        return $this->belongsTo(Site::class);
    }


    public function getUrlAttribute()
    {
        return config('app.url_lifelines').'/'.$this->encode();
    }
}

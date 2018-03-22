<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Bitt extends Model
{
    protected $guarded = ['id'];

    protected $with = [
        'systems',
        'categories',
    ];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('private', function (Builder $builder) {
            if (\Auth::check()) {
                $builder->where('private', '=', 0)
                    ->orWhere(function (Builder $builder) {
                        $builder->where('user_id', \Auth::user()->id);
                    });
            }
        });
    }

    public function categories()
    {
        return $this->morphToMany(Category::class, 'categorable');
    }

    public function systems()
    {
        return $this->belongsToMany(System::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function commandDescription($status)
    {
        return $status.' bitt '.$this->title;
    }
}

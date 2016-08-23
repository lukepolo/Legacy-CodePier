<?php

namespace App\Traits;

/**
 * This file is part of Teamwork
 *
 * @license MIT
 * @package Teamwork
 */

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

/**
 * Class UsedByTeams
 * @package Mpociot\Teamwork\Traits
 */
trait UsedByTeams
{
    /**
     * Boot the global scope
     */
    protected static function bootUsedByTeams()
    {
        if (\Auth::check()) {
            $teamworkModel = static::$teamworkModel;

            if (empty(auth()->user()->current_team_id)) {
                static::addGlobalScope('team', function (Builder $builder) use($teamworkModel) {
                    $builder->doesntHave($teamworkModel);
                });
            } else {
                static::addGlobalScope('team', function (Builder $builder) use ($teamworkModel) {

                    $builder->whereHas($teamworkModel, function ($query) {
                        $query->whereHas('users', function ($query) {
                            $query->where('user_id', auth()->user()->id);
                        })->where('team_id', auth()->user()->currentTeam->getKey());
                    });
                });

                static::saved(function (Model $model) {
                    if($model->teamworkSync) {
                        $model->teams()->attach(auth()->user()->currentTeam->getKey());
                    }
                });
            }
        }
    }

    /**
     * @param Builder $query
     * @return mixed
     */
    public function scopeAllTeams(Builder $query)
    {
        return $query->withoutGlobalScope('team');
    }

    /**
     * @return mixed
     */
    public function team()
    {
        return $this->belongsTo(Config::get('teamwork.team_model'));
    }
}

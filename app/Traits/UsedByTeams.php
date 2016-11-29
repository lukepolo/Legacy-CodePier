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
        $teamworkModel = static::$teamworkModel;

        static::addGlobalScope('team', function (Builder $builder) use ($teamworkModel) {
            if(auth()->user()->current_team_id) {
                $builder->whereHas($teamworkModel, function ($query) {

                    $query->whereHas('users', function ($query) {
                        $query->where('user_id', auth()->user()->id);
                    })->where('team_id', auth()->user()->currentTeam->getKey());
                });
            } else {
                $builder->doesntHave($teamworkModel);
            }
        });

        static::saved(function (Model $model) {
            if(auth()->user()->current_team_id && $model->teamworkSync) {
                $model->teams()->attach(auth()->user()->currentTeam->getKey());
            }
        });
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
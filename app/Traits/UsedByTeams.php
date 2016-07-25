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
        if(\Auth::check()) {
            $teamworkUserModel = null;
            if(isset(static::$teamworkUserModel)) {
                $teamworkUserModel = static::$teamworkUserModel;
            }

            if (empty(auth()->user()->currentTeam)) {
                static::addGlobalScope('team', function (Builder $builder) use($teamworkUserModel) {
                    if(!empty($teamworkUserModel)) {
                        $builder->whereHas($teamworkUserModel, function($query) {
                            $query->where('user_id', auth()->user()->id);
                        });
                    } else {
                        $builder->where('user_id', auth()->user()->id);
                    }
                });
            } else {
                static::addGlobalScope('team', function (Builder $builder) {
                    $builder->where('team_id', auth()->user()->currentTeam->getKey());
                });

                static::saving(function (Model $model) {
                    if (!isset($model->team_id)) {
                        $model->team_id = auth()->user()->currentTeam->getKey();
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

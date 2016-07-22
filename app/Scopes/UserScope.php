<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

/**
 * Class UserScope
 * @package App\Scopes
 */
class UserScope implements Scope
{
    /**
     * @param Builder $builder
     * @param Model $model
     * @return $this
     */
    public function apply(Builder $builder, Model $model)
    {
        // TODO - add teamwork to this
        return $builder->where('user_id', \Auth::user()->id);
    }
}
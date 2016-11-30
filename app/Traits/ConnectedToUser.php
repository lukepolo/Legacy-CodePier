<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait ConnectedToUser
{
    /**
     * Boot the global scope.
     */
    protected static function bootConnectedToUser()
    {
        $userModel = isset(static::$userModel) ? static::$userModel : null;
        static::addGlobalScope('user', function (Builder $builder) use ($userModel) {
            if (empty(auth()->user()->current_team_id)) {
                if (! empty($userModel)) {
                    $builder->whereHas($userModel, function (Builder $builder) {
                        $builder->where('user_id', auth()->user()->id);
                    });
                } else {
                    $builder->where('user_id', auth()->user()->id);
                }
            }
        });
    }
}

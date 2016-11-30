<?php

namespace App\Models\User;

use App\Traits\ConnectedToUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserLoginProvider extends Model
{
    use SoftDeletes, ConnectedToUser;

    protected $guarded = ['id'];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function user()
    {
        return $this->hasOne(User::class);
    }
}

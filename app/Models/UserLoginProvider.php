<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class UserLoginProvider
 * @package App\Models
 */
class UserLoginProvider extends Model
{
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

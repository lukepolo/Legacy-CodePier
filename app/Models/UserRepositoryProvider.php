<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class UserRepositoryProvider
 * @package App\Models
 */
class UserRepositoryProvider extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function repositoryProvider()
    {
        return $this->belongsTo(RepositoryProvider::class);
    }
}

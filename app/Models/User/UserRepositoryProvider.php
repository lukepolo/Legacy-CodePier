<?php

namespace App\Models\User;

use App\Models\RepositoryProvider;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class UserRepositoryProvider.
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

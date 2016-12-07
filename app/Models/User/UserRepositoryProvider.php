<?php

namespace App\Models\User;

use App\Traits\ConnectedToUser;
use App\Models\RepositoryProvider;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserRepositoryProvider extends Model
{
    use SoftDeletes, ConnectedToUser;

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

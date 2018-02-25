<?php

namespace App\Models\User;

use App\Models\Server\Provider\ServerProvider;
use App\Traits\ConnectedToUser;
use App\Traits\Encryptable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserServerProvider extends Model
{
    use SoftDeletes, ConnectedToUser, Encryptable;

    protected $guarded = ['id'];

    protected $dates = [
        'created_at',
        'updated_at',
        'expires_at',
        'deleted_at',
    ];

    protected $encryptable = [
        'token',
        'refresh_token',
        'token_secret',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function serverProvider()
    {
        return $this->belongsTo(ServerProvider::class);
    }
}

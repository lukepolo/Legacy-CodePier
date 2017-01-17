<?php

namespace App\Models\User;

use App\Traits\ConnectedToUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Server\Provider\ServerProvider;

class UserServerProvider extends Model
{
    use SoftDeletes, ConnectedToUser;

    protected $guarded = ['id'];

    protected $dates = [
        'created_at',
        'updated_at',
        'expires_in',
        'deleted_at'
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

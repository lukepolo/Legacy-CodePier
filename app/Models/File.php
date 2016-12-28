<?php

namespace App\Models;

use App\Traits\FireEvents;
use App\Traits\Encryptable;
use App\Traits\ConnectedToUser;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use Encryptable, FireEvents, ConnectedToUser;

    public static $userModel = 'fileable_type';

    protected $guarded = [
        'id',
    ];

    protected $encryptable = [
        'content',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['unencrypted_content'];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function site()
    {
        dd('TODO');
    }
}

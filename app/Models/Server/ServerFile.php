<?php

namespace App\Models;

use App\Models\Server\Server;
use App\Traits\ConnectedToUser;
use App\Traits\Encryptable;
use App\Traits\FireEvents;
use Illuminate\Database\Eloquent\Model;

class ServerFile extends Model
{
    use Encryptable, FireEvents, ConnectedToUser;

    public static $userModel = 'server';

    protected $guarded = [
        'id',
    ];

    protected $encryptable = [
        'content',
    ];

    protected $hidden = [
        'content',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function server()
    {
        return $this->belongsTo(Server::class);
    }

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['unencrypted_content'];

    /**
     * Get the administrator flag for the user.
     *
     * @return bool
     */
    public function getUnencryptedContentAttribute()
    {
        if (! empty($this->attributes['content'])) {
            return decrypt($this->attributes['content']);
        }
    }
}

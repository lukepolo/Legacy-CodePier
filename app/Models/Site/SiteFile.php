<?php

namespace App\Models\Site;

use App\Traits\Encryptable;
use App\Traits\FireEvents;
use App\Traits\ServerCommands;
use Illuminate\Database\Eloquent\Model;

class SiteFile extends Model
{
    use Encryptable, FireEvents, ServerCommands;

    protected $guarded = ['id'];

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

    public function site()
    {
        return $this->belongsTo(Site::class);
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
        return decrypt($this->attributes['content']);
    }
}

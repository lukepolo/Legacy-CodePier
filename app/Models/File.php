<?php

namespace App\Models;

use App\Models\Server\Server;
use App\Models\Site\Site;
use App\Traits\Encryptable;
use App\Traits\HasServers;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use Encryptable, HasServers;

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

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function sites()
    {
        return $this->morphedByMany(Site::class, 'fileable');
    }

    public function servers()
    {
        return $this->morphedByMany(Server::class, 'fileable');
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */
    public function delete()
    {
        $this->sites()->detach();
        $this->servers()->detach();
        parent::delete();
    }

    public function commandDescription($status)
    {
        return $status.' file '.$this->file_path;
    }
}

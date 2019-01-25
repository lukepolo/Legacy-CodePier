<?php

namespace App\Models;

use App\Models\Site\Site;
use App\Traits\HasServers;
use App\Traits\Encryptable;
use App\Models\Server\Server;
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

    protected $hidden = [
      'pivot',
      'content',
      'created_at',
    ];

    protected $appends = [
      'contents'
    ];

    public function getContentsAttribute() {
        if (! empty($this->attributes['content'])) {
            return decrypt($this->attributes['content']);
        }
    }

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

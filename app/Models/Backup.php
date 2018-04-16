<?php

namespace App\Models;

use App\Models\Site\Site;
use App\Models\Server\Server;
use Illuminate\Database\Eloquent\Model;

class Backup extends Model
{
    protected $guarded = ['id'];

    protected $appends = [
        'size'
    ];

    /**
     * Get the administrator flag for the user.
     *
     * @return bool
     */
    public function getSizeAttribute()
    {
        // https://gist.github.com/liunian/9338301#gistcomment-1970620
        if (! empty($this->attributes['size'])) {
            $bytes = $this->attributes['size'];
            $i = floor(log($bytes, 1024));
            return round($bytes / pow(1024, $i), [0,0,2,2,3][$i]).' '.['B','kB','MB','GB','TB'][$i];
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function sites()
    {
        return $this->morphedByMany(Site::class, 'backupable');
    }

    public function servers()
    {
        return $this->morphedByMany(Server::class, 'backupable');
    }
}

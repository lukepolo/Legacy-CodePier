<?php

namespace App\Models;

use App\Models\Server\Server;
use App\Models\Site\Site;
use Illuminate\Database\Eloquent\Model;

class Schema extends Model
{
    protected $guarded = ['id'];

    /*
   |--------------------------------------------------------------------------
   | Relations
   |--------------------------------------------------------------------------
   */

    public function sites()
    {
        return $this->morphedByMany(Site::class, 'schemable');
    }

    public function servers()
    {
        return $this->morphedByMany(Server::class, 'schemable');
    }

    public function users()
    {
        return $this->hasMany(SchemaUser::class);
    }

}

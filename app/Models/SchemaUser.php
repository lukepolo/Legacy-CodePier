<?php

namespace App\Models;

use App\Models\Server\Server;
use App\Models\Site\Site;
use Illuminate\Database\Eloquent\Model;

class SchemaUser extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'schema_ids' => 'array'
    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function sites()
    {
        return $this->morphedByMany(Site::class, 'schema_userable');
    }

    public function servers()
    {
        return $this->morphedByMany(Server::class, 'schema_userable');
    }

    public function schema()
    {
        return $this->belongsTo(Schema::class);
    }

}

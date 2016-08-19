<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Pile
 * @package App\Models
 */
class Pile extends Model
{
    protected $guarded = ['id'];

    public function servers()
    {
        return $this->hasMany(Server::class);
    }

    public function sites()
    {
        return $this->hasMany(Site::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

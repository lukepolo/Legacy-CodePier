<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pile extends Model
{
    protected $guarded = ['id'];

    public function servers()
    {
        return $this->hasMany(Server::class);
    }
}

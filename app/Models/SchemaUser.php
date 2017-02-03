<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchemaUser extends Model
{
    protected $guarded = ['id'];

    public function schema()
    {
        return $this->belongsTo(Schema::class);
    }
}

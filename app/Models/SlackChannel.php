<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SlackChannel extends Model
{
    protected $guarded = ['id'];

    /**
     * Get all of the owning slackable models.
     */
    public function slackable()
    {
        return $this->morphTo();
    }
}

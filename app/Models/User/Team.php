<?php

namespace App\Models\User;

use App\Models\Pile;
use Mpociot\Teamwork\TeamworkTeam;

/**
 * Class User.
 */
class Team extends TeamworkTeam
{
    public function piles()
    {
        return $this->belongsToMany(Pile::class)->withoutGlobalScope('team');
    }
}

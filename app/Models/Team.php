<?php

namespace App\Models;

use Mpociot\Teamwork\TeamworkTeam;

/**
 * Class User
 * @package App\Models
 */
class Team extends TeamworkTeam
{
    public function piles()
    {
        return $this->belongsToMany(Pile::class);
    }
}

<?php

namespace App\Models;

use App\Traits\UsedByTeams;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Pile
 * @package App\Models
 */
class Pile extends Model
{
    use UsedByTeams;

    protected $guarded = ['id'];

    static $teamworkModel = 'teams';
    public $teamworkSync = true;

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

    public function teams()
    {
        return $this->belongsToMany(Team::class);
    }
}

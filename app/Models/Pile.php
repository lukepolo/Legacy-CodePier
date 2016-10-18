<?php

namespace App\Models;

use App\Scopes\UserScope;
use App\Traits\UsedByTeams;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Pile.
 */
class Pile extends Model
{
    use UsedByTeams;

    protected $guarded = ['id'];

    public static $teamworkModel = 'teams';
    public $teamworkSync = true;

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new UserScope);
    }

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

    public function delete()
    {
        $this->teams()->detach();
        foreach ($this->servers as $server) {
            $server->pile_id = 0;
        }

        foreach ($this->sites as $site) {
            $site->pile_id = 0;
        }

        return parent::delete();
    }
}

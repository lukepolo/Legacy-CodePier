<?php

namespace App\Models;

use App\Models\Site\Site;
use App\Models\User\Team;
use App\Models\User\User;
use App\Scopes\UserScope;
use App\Traits\HasServers;
use App\Traits\UsedByTeams;
use App\Traits\ConnectedToUser;
use Illuminate\Database\Eloquent\Model;

class Pile extends Model
{
    use UsedByTeams, ConnectedToUser, HasServers;

    protected $guarded = ['id'];

    public static $teamworkModel = 'teams';
    public $teamworkSync = true;

    public $with = [
        'sites',
    ];

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new UserScope());
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

        foreach ($this->sites as $site) {
            $site->pile_id = 0;
        }

        return parent::delete();
    }
}

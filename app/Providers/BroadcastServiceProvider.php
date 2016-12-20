<?php

namespace App\Providers;

use App\Models\Site\Site;
use App\Models\Server\Server;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Broadcast;

class BroadcastServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Broadcast::routes();

        Broadcast::channel('App.Models.User.User.*', function ($user, $userId) {
            return (int) $user->id === (int) $userId;
        });

        Broadcast::channel('App.Models.User.Team.*', function ($user, $teamID) {
            return (int) $user->teams->contains((int) $teamID);
        });

        Broadcast::channel('App.Models.Server.Server.*', function ($user, $serverID) {
            return $user->id === Server::findOrFail($serverID)->user_id;
        });

        Broadcast::channel('App.Models.Site.Site.*', function ($user, $siteID) {
            return $user->id === Site::findOrFail($siteID)->user_id;
        });
    }
}

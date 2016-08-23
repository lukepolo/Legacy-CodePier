<?php

namespace App\Providers;

use App\Models\Server;
use App\Models\Site;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Broadcast;

/**
 * Class BroadcastServiceProvider
 * @package App\Providers
 */
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

        Broadcast::channel('App.Models.User.*', function ($user, $userId) {
            return (int) $user->id === (int) $userId;
        });

        Broadcast::channel('App.Models.Team.*', function ($user, $teamID) {
            return (int) $user->teams->contains((int) $teamID);
        });

        Broadcast::channel('App.Models.Server.*', function ($user, $serverID) {
            return $user->id === Server::findOrFail($serverID)->user_id;
        });

        Broadcast::channel('App.Models.Site.*', function ($user, $siteID) {
            return $user->id === Site::findOrFail($siteID)->user_id;
        });
    }
}
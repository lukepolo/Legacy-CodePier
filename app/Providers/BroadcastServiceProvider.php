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

        Broadcast::channel('App.Models.User.User.{userId}', function ($user, $userId) {
            return (int) $user->id === (int) $userId;
        });

        Broadcast::channel('App.Models.User.Team.{teamId}', function ($user, $teamId) {
            return (int) $user->teams->contains((int) $teamId);
        });

        Broadcast::channel('App.Models.Server.Server.{serverId}', function ($user, $serverId) {
            return $user->id === Server::findOrFail($serverId)->user_id;
        });

        Broadcast::channel('App.Models.Site.Site.{siteId}', function ($user, $siteId) {
            return $user->id === Site::findOrFail($siteId)->user_id;
        });
    }
}

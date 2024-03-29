<?php

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.User.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});

Broadcast::channel('App.Models.User.Team.{teamId}', function ($user, $teamId) {
    return (int) $user->teams->contains((int) $teamId);
});

Broadcast::channel('App.Models.Server.Server.{serverId}', function ($user, $serverId) {
    return $user->id === \App\Models\Server\Server::findOrFail($serverId)->user_id;
});

Broadcast::channel('App.Models.Site.Site.{siteId}', function ($user, $siteId) {
    return $user->id === \App\Models\Site\Site::findOrFail($siteId)->user_id;
});

Broadcast::channel('App.Models.Site.Lifeline.{lifeline}', function ($user, $lifeline) {
    return $user->id === \App\Models\Site\Lifeline::findOrFail($lifeline)->site->user_id;
});

Broadcast::channel('App.Models.SslCertificate.{sslCertificateId}', function ($user, $sslCertificateId) {
    $sslCertificate = \App\Models\SslCertificate::with(['sites'])->findOrfail($sslCertificateId);

    return $user->currentPile->sites->whereIn('id', $sslCertificate->sites->pluck('id'))->count();
});

<template>
    @foreach($notificationProviders as $notificationProvider)
    <p>
        Integrate with {{ $notificationProvider->name }} :
        @if(!\Auth::user()->userNotificationProviders->pluck('notification_provider_id')->contains($notificationProvider->id))
        <a href="{{ action('Auth\OauthController@newProvider', $notificationProvider->provider_name) }}" class="btn btn-default">Integrate</a>
        @else
        <a href="{{ action('Auth\OauthController@getDisconnectService', [App\Models\UserNotificationProvider::class, \Auth::user()->userNotificationProviders->first(function($userNotificationProvider) use ($notificationProvider) {
                                                return $userNotificationProvider->notification_provider_id == $notificationProvider->id;
                                            })->id]) }}">Disconnect</a>
        @endif
    </p>
    @endforeach
</template>
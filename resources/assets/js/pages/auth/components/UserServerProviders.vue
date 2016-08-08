<template>
    <p>
        @foreach($serverProviders as $serverProvider)
        Integrate with {{ $serverProvider->name }} :

        @if(!\Auth::user()->userServerProviders->pluck('server_provider_id')->contains($serverProvider->id))
        <a href="{{ action('Auth\OauthController@newProvider', $serverProvider->provider_name) }}" class="btn btn-default">Integrate</a>
        @else
        <a href="{{ action('Auth\OauthController@getDisconnectService', [App\Models\UserServerProvider::class, \Auth::user()->userServerProviders->first(function($userServerProvider) use ($serverProvider) {
                                                return $userServerProvider->server_provider_id == $serverProvider->id;
                                            })->id]) }}">Disconnect</a>
        @endif
        @endforeach
    </p>
</template>
<template>
    @foreach($repositoryProviders as $repositoryProvider)
    <p>
        Integrate with {{ $repositoryProvider->name }} :
        @if(!\Auth::user()->userRepositoryProviders->pluck('repository_provider_id')->contains($repositoryProvider->id))
        <a href="{{ action('Auth\OauthController@newProvider', $repositoryProvider->provider_name) }}" class="btn btn-default">Integrate</a>
        @else
        <a href="{{ action('Auth\OauthController@getDisconnectService', [App\Models\UserRepositoryProvider::class, \Auth::user()->userRepositoryProviders->first(function($userRepositoryProvider) use ($repositoryProvider) {
                                                return $userRepositoryProvider->repository_provider_id == $repositoryProvider->id;
                                            })->id]) }}">Disconnect</a>
        @endif
    </p>
    @endforeach
</template>
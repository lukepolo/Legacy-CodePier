@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default">
                    <div class="panel-heading">Your Profile</div>
                    <div class="panel-body">
                        <ul id="myTabs" class="nav nav-tabs">
                            <li role="presentation" class="active"><a href="#account-info" role="tab" data-toggle="tab">Account Info</a></li>
                            <li role="presentation" class=""><a href="#ssh-keys" role="tab" data-toggle="tab">SSH Keys</a></li>
                            <li role="presentation" class=""><a href="#server-providers" role="tab" data-toggle="tab">Server Providers</a></li>
                            <li role="presentation" class=""><a href="#repository-providers" role="tab" data-toggle="tab">Repository Providers</a></li>
                        </ul>
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane fade active in" id="account-info">
                                <p>
                                    tab 1
                                </p>
                            </div>
                            <div role="tabpanel" class="tab-pane fade" id="ssh-keys">
                                <p>
                                    tab 2
                                </p>
                            </div>
                            <div role="tabpanel" class="tab-pane fade" id="server-providers">
                                <p>
                                    @foreach(\App\Http\Controllers\Auth\OauthController::$serverProviders as $serverProvider)
                                        {!! Form::open(['method' => 'POST', 'action' => ['Auth\OauthController@postNewProvider', $serverProvider] ]) !!}
                                            {!! Form::label($serverProvider) !!}
                                            {!! Form::text('server_name') !!}
                                            {!! Form::submit('Integrate') !!}
                                        {!! Form::close() !!}
                                    @endforeach
                                </p>
                            </div>
                            <div role="tabpanel" class="tab-pane fade" id="repository-providers">
                                @foreach(\App\Http\Controllers\Auth\OauthController::$repositoryProviders as $repositoryProvider)
                                    <p>
                                        Integrate with {{ $repositoryProvider }} :
                                        @if(!\Auth::user()->userRepositoryProviders->lists('service')->contains($repositoryProvider))
                                            <a href="{{ action('Auth\OauthController@postNewProvider', $repositoryProvider) }}" class="btn btn-default">{{ $repositoryProvider }}</a>
                                        @else
                                            Connected
                                        @endif
                                    </p>
                                @endforeach
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

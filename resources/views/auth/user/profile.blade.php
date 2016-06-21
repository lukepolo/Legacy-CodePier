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
                                    {!! Form::open(['action' => 'Auth\UserController@postMyProfile']) !!}
                                        <div class="form-group">
                                            {!! Form::label('name') !!}
                                            {!! Form::text('name', \Auth::user()->name, ['class' => 'form-control']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('email') !!}
                                            {!! Form::email('email', \Auth::user()->email, ['class' => 'form-control']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('new password') !!}
                                            {!! Form::password('new-password', ['class' => 'form-control']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('confirm password') !!}
                                            {!! Form::password('confirm-password', ['class' => 'form-control']) !!}
                                        </div>
                                        {!! Form::submit('Update Profile') !!}
                                    {!! Form::close() !!}
                                </p>
                            </div>
                            <div role="tabpanel" class="tab-pane fade" id="ssh-keys">
                                {!! Form::open(['action' => ['Auth\UserController@postAddSshKey', 1]]) !!}
                                <div class="form-group">
                                    {!! Form::label('name') !!}
                                    {!! Form::text('name', null, ['class' => 'form-control']) !!}
                                </div>
                                <div class="form-group">
                                    {!! Form::label('Public Key') !!}
                                    {!! Form::textarea('ssh_key', null, ['class' => 'form-control']) !!}
                                </div>
                                {!! Form::submit('Install SSH Key') !!}
                                {!! Form::close() !!}
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th>Key Name</th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach(\Auth::user()->sshKeys as $sshKey)
                                        <tr>
                                            <td>{{ $sshKey->name }}</td>
                                            <td><a href="{{ action('Auth\UserController@getRemoveSshKey', $sshKey->id) }}" class="fa fa-remove"></a></td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div role="tabpanel" class="tab-pane fade" id="server-providers">
                                <p>
                                    @foreach(\App\Http\Controllers\Auth\OauthController::$serverProviders as $serverProvider)
                                        {!! Form::open(['method' => 'POST', 'action' => ['Auth\OauthController@newProvider', $serverProvider] ]) !!}
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
                                            <a href="{{ action('Auth\OauthController@newProvider', $repositoryProvider) }}" class="btn btn-default">{{ $repositoryProvider }}</a>
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

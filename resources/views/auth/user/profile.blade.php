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
                                    {!! Form::open(['method' => 'POST', 'action' => 'Auth\OauthController@postNewProvider']) !!}
                                        {!! Form::select('server_provider', ['digital_ocean']) !!}
                                        {!! Form::text('server_name') !!}
                                        {!! Form::submit('Integrate') !!}
                                    {!! Form::close() !!}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

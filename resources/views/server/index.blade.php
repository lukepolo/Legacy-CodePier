@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default">
                    <div class="panel-heading">Server {{ $server->name }} <small>{{ $server->ip }}</small></div>
                    <div class="panel-body">
                        <ul id="tabs" class="nav nav-tabs" data-tabs="tabs">
                            <li class="active"><a href="#sites" data-toggle="tab">Sites</a></li>
                            <li><a href="#ssh_keys" data-toggle="tab">SSH Keys</a></li>
                            <li><a href="#cron_jobs" data-toggle="tab">Cron Jobs</a></li>
                            <li><a href="#daemons" data-toggle="tab">Daemons</a></li>
                            <li><a href="#firewall" data-toggle="tab">Firewall</a></li>
                        </ul>
                        <div id="my-tab-content" class="tab-content">
                            <div class="tab-pane active" id="sites">
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th>Domain</th>
                                        <th>Repository</th>
                                        <th>ZeroTime Deployment</th>
                                        <th>Workers</th>
                                        <th>WildCard Domain</th>
                                        <th>SSL</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($server->sites as $site)
                                        <tr>
                                            <td><a href="{{ action('SiteController@getSite', [$server->id, $site->id]) }}">{{ $site->domain }}</a></td>
                                            <td>{{ $site->repository }}</td>
                                            <td>{{ $site->zerotime_deployment }}</td>
                                            <td>{{ $site->wildcard_domain }}</td>
                                            <td>Inactive</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>

                                @include('server.site.form')
                            </div>
                            <div class="tab-pane" id="ssh_keys">
                                {!! Form::open(['action' => ['ServerController@postInstallSshKey', $server->id]]) !!}
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
                                        @foreach($server->sshKeys as $sshKey)
                                            <tr>
                                                <td>{{ $sshKey->name }}</td>
                                                <td><a href="{{ action('ServerController@getRemoveSshKey', [$server->id, $sshKey->id]) }}" class="fa fa-remove"></a></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="tab-pane" id="cron_jobs">
                                cron_jobs
                            </div>
                            <div class="tab-pane" id="daemons">
                                daemons
                            </div>
                            <div class="tab-pane" id="firewall">
                                firewall
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

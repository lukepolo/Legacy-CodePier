@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        Server {{ $server->name }} <small>{{ $server->ip }}</small>
                    </div>
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
                                            <td>0</td>
                                            <td>{{ $site->wildcard_domain }}</td>
                                            <td>{{ $site->hasSSL() ? $site->ssl->type : false }}</td>
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
                                {!! Form::open(['action' => ['ServerController@postInstallCronJob', $server->id]]) !!}
                                    {!! Form::text('cron') !!}
                                    {!! Form::submit('Create cron') !!}
                                {!! Form::close() !!}

                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th>Job</th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($server->cronJobs as $cronJob)
                                        <tr>
                                            <td>{{ $cronJob->job }}</td>
                                            <td><a href="{{ action('ServerController@getRemoveCronJob', [$server->id, $cronJob->id]) }}" class="fa fa-remove"></a></td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>

                            </div>
                            <div class="tab-pane" id="daemons">
                                {!! Form::open(['action' => ['ServerController@postAddDaemon', $server->id]]) !!}

                                Command
                                {!! Form::text('command') !!}
                                User
                                {!! Form::text('user') !!}
                                {!! Form::checkbox('auto_start') !!} Auto Start
                                {!! Form::checkbox('auto_restart') !!} Auto Restart
                                Workers
                                {!! Form::text('number_of_workers') !!}

                                {!! Form::submit('Create cron') !!}
                                {!! Form::close() !!}

                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th>Command</th>
                                        <th>User</th>
                                        <th>Auto Start</th>
                                        <th>Auto Restart</th>
                                        <th>Number of Workers</th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($server->daemons as $daemon)
                                        <tr>
                                            <td>{{ $daemon->command }}</td>
                                            <td>{{ $daemon->user }}</td>
                                            <td>{{ $daemon->auto_start }}</td>
                                            <td>{{ $daemon->auto_restart }}</td>
                                            <td>{{ $daemon->number_of_workers }}</td>
                                            <td><a href="{{ action('ServerController@getRemoveDaemon', [$server->id, $daemon->id]) }}" class="fa fa-remove"></a></td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="tab-pane" id="firewall">
                                <div class="row">
                                    Connect to :
                                    {!! Form::open(['action' => ['ServerController@postAddServerNetworkRules', $server->id]]) !!}
                                        @foreach($servers as $server)
                                             <div class="checkbox">
                                                 <label>
                                                     {!! Form::checkbox('servers[]', $server->id) !!} {{ $server->name }} - {{ $server->ip }}
                                                 </label>
                                             </div>
                                        @endforeach
                                        {!! Form::submit('Connect to Servers') !!}
                                    {!! Form::close() !!}
                                </div>
                                <div class="row">

                                    {!! Form::open(['action' => ['ServerController@postAddFirewallRule', $server->id]]) !!}
                                        description
                                        {!! Form::text('description') !!}
                                        from ip
                                            {!! Form::text('from_ip') !!}
                                        port
                                        {!! Form::text('port') !!}
                                    {!! Form::submit('Create Rule') !!}
                                    {!! Form::close() !!}

                                    <table class="table">
                                        <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>From IP</th>
                                            <th>Port</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($server->firewallRules as $firewallRule)
                                            <tr>
                                                <td>{{ $firewallRule->description }}</td>
                                                <td>{{ $firewallRule->port }}</td>
                                                <td><a href="{{ action('ServerController@getRemoveFireWallRule', [$server->id, $firewallRule->id]) }}" class="fa fa-remove"></a></td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <a href="#" class="btn btn-xs">Archive Server</a>

                <a href="#" class="btn btn-xs">Restart Nginx & PHP-FPM</a>
                <a href="#" class="btn btn-xs">Restart Server</a>
                <a href="#" class="btn btn-xs">Restart Database</a>
                <a href="#" class="btn btn-xs">Restart Workers</a>

                <a href="#" class="btn btn-xs">Edit PHP Config</a>
                <a href="#" class="btn btn-xs">Edit PHP CLI Config</a>
            </div>
        </div>
    </div>
@endsection

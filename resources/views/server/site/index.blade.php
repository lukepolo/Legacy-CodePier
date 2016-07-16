@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        Domain : {{ $site->domain }}

                        {!! Form::open(['action' => ['SiteController@postRenameDomain', $site->server->id, $site->id]]) !!}
                            {!! Form::label('Domain') !!}
                            {!! Form::text('domain', $site->domain) !!}
                            {!! Form::submit('Rename') !!}
                        {!! Form::close() !!}

                        <div class="pull-right">
                            {{ $site->server->ip }}
                        </div>
                    </div>
                    <div class="panel-body">
                        <ul id="tabs" class="nav nav-tabs" data-tabs="tabs">
                            <li class="active"><a href="#repository" data-toggle="tab">Repositories</a></li>
                            <li><a href="#environment" data-toggle="tab">Environment</a></li>
                            <li><a href="#workers" data-toggle="tab">Workers</a></li>
                            <li><a href="#ssl_certs" data-toggle="tab">SSL Certificates</a></li>
                        </ul>
                        <div id="my-tab-content" class="tab-content">
                            <div class="tab-pane active" id="repository">
                                {!! Form::open(['action' => ['SiteController@postInstallRepository', $site->server->id, $site->id]]) !!}
                                    <div class="form-group">
                                        {!! Form::label('Repository') !!}
                                        {!! Form::text('repository', $site->repository, ['class' => 'form-control']) !!}
                                    </div>
                                    <div class="form-group checkbox">
                                        <label>
                                            {!! Form::hidden('zerotime_deployment', false) !!}
                                            {!! Form::checkbox('zerotime_deployment', true, $site->zerotime_deployment) !!} Zerotime Deployment
                                        </label>
                                    </div>
                                    <div class="form-group">
                                        {!! Form::label('Path') !!}
                                        {!! Form::text('path', $site->path, ['class' => 'form-control']) !!}
                                    </div>
                                    <div class="form-group">
                                        {!! Form::label('branch') !!}
                                        {!! Form::text('branch', $site->branch, ['class' => 'form-control']) !!}
                                    </div>
                                    {!! Form::submit('Install Repository') !!}
                                {!! Form::close() !!}

                                <a href="{{ action('SiteController@getDeploy', [$site->server_id, $site->id]) }}" class="btn btn-primary">Deploy</a>
                            </div>
                            <div class="tab-pane" id="environment">
                                {!! Form::open(['action' => ['SiteController@postEnv', $site->server->id, $site->id]]) !!}
                                <textarea name="env">

                                </textarea>
                                {!! Form::submit('Update Env') !!}
                                {!! Form::close() !!}
                            </div>
                            <div class="tab-pane" id="workers">
                                Laravel Queue Workers
                                {!! Form::open(['action' => ['SiteController@postInstallWorker', $site->server->id, $site->id]]) !!}
                                    Connection
                                    {!! Form::text('connection', 'beanstalkd') !!}
                                    Queue
                                    {!! Form::text('queue', 'default') !!}
                                    Maximum Seconds Per Job
                                    {!! Form::text('timeout', '60') !!}
                                    Time interval between jobs (when empty)
                                    {!! Form::text('sleep', '10') !!}
                                    Maximum Tries
                                    {!! Form::text('tries', '3') !!}
                                    Run As Daemon
                                    {!! Form::hidden('daemon', false) !!}
                                    {!! Form::checkbox('daemon', 'true') !!}
                                    {!! Form::submit('Install Worker') !!}
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
                                    @foreach($site->daemons as $daemon)
                                        <tr>
                                            <td>{{ $daemon->command }}</td>
                                            <td>{{ $daemon->user }}</td>
                                            <td>{{ $daemon->auto_start }}</td>
                                            <td>{{ $daemon->auto_restart }}</td>
                                            <td>{{ $daemon->number_of_workers }}</td>
                                            <td><a href="{{ action('SiteController@getRemoveWorker', [$site->server_id, $site->id, $daemon->id]) }}" class="fa fa-remove"></a></td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>

                            </div>
                            <div class="tab-pane" id="ssl_certs">
                                {!! Form::open(['action' => ['SiteController@postRequestLetsEncryptSSLCert', $site->server_id, $site->id]]) !!}
                                    {!! Form::label('Domains') !!}
                                    {!! Form::text('domains', $site->domain) !!}
                                    {!! Form::submit('Request SSL') !!}
                                {!! Form::close() !!}

                                @if($site->hasSsl())
                                    {{ $site->ssl->type }} : {{ $site->ssl->domains }}
                                    <a href="{{ action('SiteController@getRemoveSSL', [$site->server_id, $site->id]) }}">X</a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script type="text/javascript">
        var editor = $('#environment').find('textarea');

        $(document).on('click', 'li a[href="#environment"]', function() {
            renderEnvironmentEditor();
        });
        function renderEnvironmentEditor() {
            CodeMirror.fromTextArea(editor[0], {
                mode: 'shell',
                lineNumbers: true,
                matchBrackets: true
            });
        }

        $.get('{{ action('SiteController@getEnv', [$site->server_id, $site->id]) }}', function(envFile) {
            editor.html(envFile);
        });

    </script>
@endpush
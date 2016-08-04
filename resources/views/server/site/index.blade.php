@extends('layouts.app')

@section('content')
    <style>
        .editor {
            position: relative;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
        }
    </style>
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
                            
                            <li><a href="#ssl-certs" data-toggle="tab">SSL Certificates</a></li>
                            <li><a href="#php-settings" data-toggle="tab">PHP Common Settings</a></li>
                            <li><a href="#edit-files" data-toggle="tab">Edit Files</a></li>
                        </ul>
                        <div id="my-tab-content" class="tab-content">
                            <div class="tab-pane active" id="repository">
                                @if(empty($site->repository))
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
                                            {!! Form::label('branch') !!}
                                            {!! Form::text('branch', $site->branch, ['class' => 'form-control']) !!}
                                        </div>
                                        <div class="form-group">
                                            @foreach(\Auth::user()->userRepositoryProviders as $userRepositoryProvider)
                                                <div class="radio">
                                                    <label>
                                                        {!! Form::radio('user_repository_provider_id', $userRepositoryProvider->id) !!} {{ $userRepositoryProvider->repositoryProvider->name }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                        {!! Form::submit('Install Repository') !!}
                                    {!! Form::close() !!}
                                @else
                                    <a href="{{ action('SiteController@getRemoveRepository', [$site->server_id, $site->id]) }}">Remove Repoisotry</a>
                                @endif

                                {!! Form::open(['action' => ['SiteController@postUpdateWebDirectory', $site->server->id, $site->id]]) !!}
                                    {!! Form::text('web_directory', $site->web_directory) !!}
                                    {!! Form::submit('Updated Web Directory') !!}
                                {!! Form::close() !!}

                                <a href="{{ action('SiteController@getDeploy', [$site->server_id, $site->id]) }}" class="btn btn-primary">Deploy</a>

                                @if(empty($site->automatic_deployment_id))
                                    <a href="{{ action('SiteController@getCreateDeployHook', [$site->server_id, $site->id]) }}" class="btn btn-primary">Automatic Deployments</a>
                                @else
                                    <a href="{{ action('SiteController@getDeleteDeployHook', [$site->server_id, $site->id]) }}" class="btn btn-primary">Stop Automatic Deployments</a>
                                @endif

                            </div>
                            <div class="tab-pane" id="environment">
                                {!! Form::open(['action' => ['ServerController@postSaveFile', $site->server_id]]) !!}
                                    <div data-url="{{ action('ServerController@getFileFromServer', $site->server_id) }}" data-path="{{ '/home/codepier/'.$site->domain . '/.env' }}" class="editor">Loading . . . </div>
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
                            <div class="tab-pane" id="ssl-certs">

                                <a href="#" class="btn btn-xs">Create Signing Request</a>
                                <a href="#" class="btn btn-xs">Install Certificate</a>
                                {!! Form::open(['action' => ['SiteController@postRequestLetsEncryptSSLCert', $site->server_id, $site->id]]) !!}
                                    {!! Form::label('Domains') !!}
                                    {!! Form::text('domains', $site->domain) !!}
                                    {!! Form::submit('Request SSL') !!}
                                {!! Form::close() !!}

                                @if($site->hasActiveSSL())
                                    {{ $site->activeSSL->type }} : {{ $site->activeSSL->domains }}
                                    <a href="{{ action('SiteController@getRemoveSSL', [$site->server_id, $site->id]) }}">X</a>
                                @endif
                            </div>

                            <div class="tab-pane" id="php-settings">
                                {!! Form::open(['action' => ['SiteController@postSavePHPSettings', $site->server_id, $site->id]]) !!}
                                    Max Upload Size
                                    {!! Form::text('max_upload_size', !empty($site->settings) && isset($site->settings->data['max_upload_size']) ? $site->settings->data['max_upload_size']: null) !!} MB
                                    {!! Form::submit('Update') !!}
                                {!! Form::close() !!}
                            </div>

                            <div class="tab-pane" id="edit-files">
                                <div class="row">
                                    {!! Form::open(['action' => ['ServerController@postSaveFile', $site->server_id]]) !!}
                                        <div data-url="{{ action('ServerController@getFileFromServer', $site->server_id) }}" data-path="/etc/nginx/codepier-conf/{{ $site->domain }}/server/listen" class="editor">Loading . . . </div>
                                        {!! Form::submit('Update Nginx Listen Config') !!}
                                    {!! Form::close() !!}

                                    {!! Form::open(['action' => ['ServerController@postSaveFile', $site->server_id]]) !!}
                                        <div data-url="{{ action('ServerController@getFileFromServer', $site->server_id) }}" data-path="/etc/nginx/sites-enabled/{{ $site->domain }}" class="editor">Loading . . . </div>
                                        {!! Form::submit('Update Nginx Config') !!}
                                    {!! Form::close() !!}
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <a href="{{ action('ServerController@getRestartWebServices', $site->server_id) }}" class="btn btn-xs">Restart Web Services</a>
                <a href="{{ action('ServerController@getRestartServer', $site->server_id) }}" class="btn btn-xs">Restart Server</a>
                <a href="{{ action('ServerController@getRestartDatabase', $site->server_id) }}" class="btn btn-xs">Restart Database</a>
                <a href="{{ action('ServerController@getRestartWorkers', $site->server_id) }}" class="btn btn-xs">Restart Workers</a>

                <a href="{{ action('SiteController@getDeleteSite', [$site->server_id, $site->id]) }}" class="btn btn-xs">Delete Site</a>
            </div>
        </div>
        @if(!empty($currentDeployment = $site->lastDeployment))
            <div class="row">
                <div class="col-md-10 col-md-offset-1">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            {{ ucwords($currentDeployment->status) }} - <a href="#" target="_blank">{{ $currentDeployment->git_commit }}</a>
                        </div>
                        <div class="panel-body">
                            @foreach($currentDeployment->events as $event)
                                <div class="panel panel-default">
                                    <div class="panel-body">
                                        {{ $event->step->step }} -
                                        @if($event->started && !$event->completed)
                                            Running . . .
                                        @else
                                            @if($event->completed)
                                                @if($event->failed)
                                                    FAILED
                                                @else
                                                    Completed in {{ round($event->runtime, 2) }} seconds
                                                @endif
                                                <br>
                                                @if(is_array($event->log))
                                                    @foreach($event->log as $logItem)
                                                        @if(!empty($logItem = trim(preg_replace('/codepier-done/', '', $logItem))))
                                                                {!! nl2br($logItem) !!}
                                                        @endif
                                                    @endforeach
                                                @else
                                                    {!! str_replace('\n', '<br>', trim(nl2br($event->log), '"')) !!}
                                                @endif
                                            @else
                                                Pending
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div>
                    @if(is_array($currentDeployment->log ))
                        <pre>@foreach($currentDeployment->log as $logItem) @if(!empty($logItem = trim(preg_replace('/codepier-done/', '', $logItem)))){!! $logItem !!}<br>@endif @endforeach</pre>
                    @endif
                    </div>
                </div>
            </div>
        @endif


        @foreach($site->deployments as $deployment)
            <div class="panel-heading">
                {{ ucwords($deployment->status) }} - <a href="#" target="_blank">{{ $deployment->git_commit }}</a> <a href="#">(Revert)</a>
            </div>
        @endforeach
    </div>
@endsection
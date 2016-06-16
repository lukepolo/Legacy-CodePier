@inject('serverService', 'App\Services\Server\ServerService')
<div class="row">
    <div class="col-md-10 col-md-offset-1">
        <div class="panel panel-default">
            <div class="panel-heading">
                Create A Server
            </div>
            <div class="panel-body">
                <ul id="tabs" class="nav nav-tabs" data-tabs="tabs">
                    @foreach($userServerProviders as $userServerProvider)
                        <li class="active"><a href="#{{ $userServerProvider->serverProvider->provider_name }}" data-toggle="tab">{{ $userServerProvider->serverProvider->name }}</a></li>
                    @endforeach
                </ul>
                <div id="my-tab-content" class="tab-content">
                    @foreach($userServerProviders as $userServerProvider)
                        <div class="tab-pane active" id="{{ $userServerProvider->provider_name }}">
                            {!! Form::open(['action' => 'ServerController@postCreateServer']) !!}
                            {!! Form::hidden('service', $userServerProvider->server_provider_id) !!}
                            <div class="form-group">
                                {!! Form::label('Name') !!}
                                {!! Form::text('name', null, ['class' => 'form-control']) !!}
                            </div>
                            <div class="form-group">
                                {!! Form::label('Server Option') !!}
                                <select class="form-control" name="server_option">
                                    @foreach($userServerProvider->serverProvider->serverOptions as $serverOption)
                                            <option value="{{ $serverOption->id }}">{{ $serverOption->formatToSring() }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                {!! Form::label('Server Region') !!}
                                <select class="form-control" name="server_region">
                                    @foreach($userServerProvider->serverProvider->serverRegions->sortBy('name') as $serverRegion)
                                        <option value="{{ $serverRegion->id }}">{{ $serverRegion->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            // TODO - enable custom features , based on provider / provisioning

                            <div class="form-group">
                                {!! Form::label('Database') !!}
                                {!! Form::text('database', null, ['class' => 'form-control']) !!}
                            </div>
                            {!! Form::submit('Create Server') !!}

                            {!! Form::close() !!}
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
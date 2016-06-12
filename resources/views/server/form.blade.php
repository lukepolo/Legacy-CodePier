<div class="row">
    <div class="col-md-10 col-md-offset-1">
        <div class="panel panel-default">
            <div class="panel-heading">
                Create A Server
            </div>
            <div class="panel-body">
                <ul id="tabs" class="nav nav-tabs" data-tabs="tabs">
                    @foreach($serverProviders as $serverProvider)
                        <li class="active"><a href="#{{ $serverProvider->service }}" data-toggle="tab">{{ $serverProvider->service }}</a></li>
                    @endforeach
                </ul>
                <div id="my-tab-content" class="tab-content">
                    @foreach($serverProviders as $serverProvider)
                        <div class="tab-pane active" id="{{ $serverProvider->service }}">
                            {!! Form::open(['action' => 'ServerController@postCreateServer']) !!}
                            {!! Form::hidden('service', $serverProvider->service) !!}
                            <div class="form-group">
                                {!! Form::label('Name') !!}
                                {!! Form::text('name', '', ['class' => 'form-control']) !!}

                                {!! Form::submit('Create Server') !!}
                            </div>
                            {!! Form::close() !!}
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
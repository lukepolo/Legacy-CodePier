@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        Site Details
                        <div class="pull-right">
                            server info up here
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
                                    <div class="form-group">
                                        {!! Form::label('branch') !!}
                                        {!! Form::text('branch', $site->branch, ['class' => 'form-control']) !!}
                                    </div>
                                    {!! Form::submit('Install Repository') !!}
                                {!! Form::close() !!}

                                <a href="{{ action('SiteController@getDeploy', [$site->server_id, $site->id]) }}" class="btn btn-primary">Deploy</a>
                            </div>
                            <div class="tab-pane" id="environment">
                                <pre>

                                </pre>
                            </div>
                            <div class="tab-pane" id="workers">
                                Workers
                            </div>
                            <div class="tab-pane" id="ssl_certs">
                                SSL Certs
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
        $.get('{{ action('SiteController@getEnv', [$site->server_id, $site->id]) }}', function(envFile) {
            $('#environment').find('pre').html(envFile);
        });
    </script>
@endpush
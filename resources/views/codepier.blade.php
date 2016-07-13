@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default">
                    <table class="table" v-if="servers">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Provider</th>
                                <th>IP</th>
                                <th>Status</th>
                                <th>SSH Connection</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="server in servers">
                                <td>@{{ server.name }}</td>
                                <td>@{{ server.server_provider.name }}</td>
                                <td>@{{ server.ip }}</td>
                                <td>
                                    @{{ server.status }}
                                    <div class="progress" v-if="server.progress < 99">
                                        <div class="progress-bar" role="progressbar" aria-valuenow="@{{ server.progress }}" aria-valuemin="0" aria-valuemax="100" style="width: @{{ server.progress }}%;">
                                            @{{ server.progress }}% Complete
                                        </div>
                                    </div>
                                </td>
                                <td>@{{ server.ssh_connection }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script type="text/javascript">
        var servers = {!! $servers->keyBy('id') !!};

        @if(env('NODE_ON'))
           socket.on('{{ addslashes(\App\Events\Server\ServerProvisionStatusChanged::class) }}', function(data) {
            var server = 'servers['+ data.serverID +']';
            vue.$set(server+'.status', data.status);
            vue.$set(server+'.progress', data.progress);

        });
        @endif

    </script>
    <script src="{{ asset('/js/app.js') }}"></script>
@endpush
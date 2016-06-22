@extends('layouts.app')

@section('content')
    @inject('serverService', 'App\Services\Server\ServerService')
    <div class="container">
        @if(\Auth::user()->userServerProviders->count())
            @include('server.form')
        @else
            <div class="alert alert-danger">Please connect a <a href="{{ action('Auth\UserController@getMyProfile') }}">server provider</a></div>
        @endif
        @if($servers->count())
            <div class="row">
                <div class="col-md-10 col-md-offset-1">
                    <div class="panel panel-default">
                        <div class="panel-heading">Servers</div>
                        <div class="panel-body">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Provider</th>
                                        <th>IP</th>
                                        <th>Status</th>
                                        <th>Connection</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach($servers as $server)
                                        <tr>
                                            <td><a href="{{ action('ServerController@getServer', $server->id) }}">{{ $server->name }}</a></td>
                                            <td>{{ $server->service }}</td>
                                            <td>{{ $server->ip }}</td>
                                            <td>@if($server->updated_at->diffInMinutes(\Carbon\Carbon::now()) < 15 || $server->status == 'Provisioning') {{ $server->status }} @else {{ $serverService->getStatus($server) }} @endif </td>
                                            <td> // TODO - Make sure we can SSH into the server</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

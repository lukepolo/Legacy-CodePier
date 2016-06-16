@extends('layouts.app')

@section('content')
<div class="container">
    @include('server.form')
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
                                <td>{{ $server->status }}</td>
                                <td> // TODO - figure out how to make sure things are connected</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
</div>

@endsection

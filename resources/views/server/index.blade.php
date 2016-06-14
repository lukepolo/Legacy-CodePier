@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default">
                    <div class="panel-heading">Server {{ $server->name }} <small>{{ $server->ip }}</small></div>
                    <div class="panel-body">
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
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

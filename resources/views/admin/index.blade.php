@extends('layouts.app')

@section('content')
    <div class="container">
        <a href="{{ action('AdminController@getServerOptionsAndRegions', \App\Models\ServerProvider::first()) }}">Generate Server Options and Regions</a>
    </div>
@endsection

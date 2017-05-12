<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>CodePier</title>

        <!-- Styles -->
        <link href="{{ elixir('css/app.css') }}" rel="stylesheet">

    </head>
    <body>
        STYLE GUIDE!

        @yield('content')
    </body>
</html>

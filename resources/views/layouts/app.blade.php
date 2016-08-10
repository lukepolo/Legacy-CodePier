<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>CodePier</title>

        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato:100,300,400,700">

         <link href="{{ elixir('css/app.css') }}" rel="stylesheet">
    </head>
    <body>
        <div id="app-layout">
            <navigation></navigation>

            <div id="main">
                @yield('content')
            </div>

            <app-footer></app-footer>
        </div>

        <script src="{{ elixir('js/all.js') }}"></script>

        @if(\Auth::check())
            @include('layouts.core.notifications')
        @endif

        <script type="text/javascript">
            moment.tz.setDefault("UTC");
        </script>

        @stack('scripts')
        <script src="{{ asset('/js/app.js') }}"></script>
    </body>
</html>

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
        <span id="app-layout">
            <navigation></navigation>

            <div class="main">
                @yield('content')
            </div>

            <footer></footer>
        </span>

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

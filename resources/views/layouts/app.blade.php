<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>CodePier</title>

        <!-- Styles -->
        <link href="{{ elixir('css/app.css') }}" rel="stylesheet">

        <!-- Scripts -->
        <script>
            window.Laravel = <?php echo json_encode([
                    'csrfToken' => csrf_token(),
            ]); ?>
        </script>
    </head>
    <body>
        <div id="app-layout">
            <navigation></navigation>

            <div id="main">
                @yield('content')
            </div>

            <app-footer></app-footer>
        </div>

        <!-- Scripts -->
        <script src="{{ elixir('js/all.js') }}"></script>

        <script type="text/javascript">
            moment.tz.setDefault("UTC");
        </script>

        @stack('scripts')
        @if(\Auth::check())
            <script src="{{ asset('/js/app.js') }}"></script>
            @include('layouts.core.notifications')
        @endif
    </body>
</html>

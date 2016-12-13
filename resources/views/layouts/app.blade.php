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

        @if(env('APP_ENV') == 'production')
            <script src="https://cdn.ravenjs.com/3.8.1/raven.min.js"></script>
            <script>
                Raven.config('{{ env('SENTRY_JS') }}').install()
            </script>
        @endif

        <!-- Scripts -->
        <script>
            window.Laravel = <?php echo json_encode([
                'env' => env('APP_ENV'),
                'csrfToken' => csrf_token(),
                'pusherKey' => env('PUSHER_KEY'),
                'defaultNotificationTypes' => \App\Http\Controllers\EventController::DEFAULT_TYPES,
                'app_registration' => env('APP_REGISTRATION'),
                'version' => app()->make('gitCommit'),
            ]); ?>
        </script>
    </head>
    <body>
        <div id="app-layout">
            <navigation></navigation>

            <div id="main">
                @yield('content')
            </div>

            <notification-bar></notification-bar>
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

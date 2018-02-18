<!doctype html>
<html lang="{{ config('app.locale') }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        @include('layouts.core.favicon')

        <title>CodePier</title>

        <!-- Styles -->
        <link href="{{ mix('/css/app.css') }}" rel="stylesheet">

        <!-- DNS Prefetch -->
        <link rel="dns-prefetch" href="//fonts.gstatic.com">
        <link rel="dns-prefetch" href="//fonts.googleapis.com">

        <!-- Scripts -->
        <script>
            window.Laravel = <?php echo json_encode([
                'env' => config('app.env'),
                'csrfToken' => csrf_token(),
                'teams' => config('app.teams'),
                'version' => app()->make('gitCommit'),
                'stripeKey' => config('services.stripe.key'),
                'echoServerKey' => config('broadcasting.connections.pusher.key'),
                'serverTypes' => \App\Services\Systems\SystemService::SERVER_TYPES,
                'defaultNotificationTypes' => \App\Http\Controllers\EventController::DEFAULT_TYPES,
            ]); ?>
        </script>
    </head>
    <body>
        <div id="app-layout">
            <events-bar full-screen="true"></events-bar>
        </div>

        @stack('scripts')

        @if(\Auth::check())
            <!-- Scripts -->
            <script src="{{ mix('/js/manifest.js') }}"></script>
            <script src="{{ mix('/js/vendor.js') }}"></script>
            <script src="{{ mix('/js/app.js') }}"></script>
        @endif
    </body>
</html>

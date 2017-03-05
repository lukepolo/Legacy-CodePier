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

        @if(env('APP_ENV') == 'production')
            <script src="https://cdn.ravenjs.com/3.8.1/raven.min.js"></script>
            <script>
                Raven.config('{{ env('SENTRY_JS') }}').install()
            </script>
        @endif

        <!-- Scripts -->
        <script>
            window.Laravel = <?php echo json_encode([
                'env' => config('app.env'),
                'csrfToken' => csrf_token(),
                'pusherKey' => config('broadcasting.connections.pusher.key'),
                'defaultNotificationTypes' => \App\Http\Controllers\EventController::DEFAULT_TYPES,
                'app_registration' => config('app.registration'),
                'version' => app()->make('gitCommit'),
                'teams' => config('app.teams'),
            ]); ?>
        </script>
    </head>
    <body>
        <div id="app-layout">
            <navigation></navigation>

            <div id="xlarge-wrap">

                <div id="main">
                    @yield('content')
                </div>

                <notification-bar></notification-bar>

            </div>
        </div>

        <!-- Scripts -->
        <script src="{{ elixir('js/all.js') }}"></script>

        <script type="text/javascript">
            moment.tz.setDefault("UTC");
        </script>

        @stack('scripts')

        @if(\Auth::check())
            <script src="{{ elixir('js/app.js') }}"></script>
            @include('layouts.core.notifications')
            @if(config('app.env') == 'production')
                <script type="text/javascript">
                    $crisp=[];CRISP_WEBSITE_ID="144f48f7-3604-4483-a8e1-107106d86484";(function(){d=document;s=d.createElement("script");s.src="https://client.crisp.im/l.js";s.async=1;d.getElementsByTagName("head")[0].appendChild(s);})();
                    $crisp.push(["set", "user:email", "{{ auth()->user()->email }}"]);
                    $crisp.push(["set", "user:nickname", "({{ auth()->user()->id }} ) {{ auth()->user()->name }} "]);
                </script>
            @endif
        @endif

        @if($errors->count())
            <script>
                app.showError('{{ $errors->first() }}', null, 0)
            </script>
        @endif
    </body>
</html>

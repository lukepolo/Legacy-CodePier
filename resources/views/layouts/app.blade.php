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
    <body id="app-layout">
        @include('layouts.core.navigation')

        @include('layouts.core.alerts')
        @include('layouts.core.errors')

        @yield('content')

        @include('layouts.core.footer')

        <script src="{{ elixir('js/all.js') }}"></script>

        @if(env('NODE_ON', false))
            @include('layouts.core.socketio')
        @endif

        <script type="text/javascript">

            @if(env('NODE_ON'))
                socket.on('{{ addslashes(\App\Events\Server\Site\DeploymentCompleted::class) }}', function(data) {
                    if (Notification) {
                        if (Notification.permission !== "granted") {
                            Notification.requestPermission();
                        }

                        // https://github.com/realtime-framework/ChromePushNotifications
                        // https://developer.mozilla.org/en-US/docs/Web/API/notification
                        var notification = new Notification(data.event.description, {
                            icon: 'https://s32.postimg.org/m0n5f5in9/pasted_image_at_2016_07_21_04_03_pm.png',
                            body : data.event.data
                        });

                        notification.onclick = function(event) {
                            event.preventDefault(); // prevent the browser from focusing the Notification's tab
                            window.open('http://codepier.app/server/3', '_blank');
                        };
                    }
                });

                socket.on('{{ addslashes(\App\Events\Server\Site\DeploymentFailed::class) }}', function(data) {
                    if (Notification) {
                        if (Notification.permission !== "granted") {
                            Notification.requestPermission();
                        }

                        // https://github.com/realtime-framework/ChromePushNotifications
                        // https://developer.mozilla.org/en-US/docs/Web/API/notification
                        var notification = new Notification(data.event.description, {
                            icon: 'https://shortpolo.com/assets/screenshots/2z.png',
                            body : data.event.data
                        });

                        notification.onclick = function(event) {
                            event.preventDefault(); // prevent the browser from focusing the Notification's tab
                            window.open('http://codepier.app/server/3', '_blank');
                        };
                    }
                });

            @endif



            var vue;

            moment.tz.setDefault("UTC");

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        </script>

        @stack('scripts')
    </body>
</html>

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

            if (Notification) {
                if (Notification.permission !== "granted") {
                    Notification.requestPermission();
                }

//                // https://developer.mozilla.org/en-US/docs/Web/API/notification
//                var notification = new Notification('Server Provisioned', {
//                    icon: 'https://www.dropbox.com/s/vq20qxb343qkqov/pasted_image_at_2016_07_21_04_03_pm.png?dl=0',
//                    body : 'Get going!'
//                });
//
//                notification.onclick = function(event) {
//                    event.preventDefault(); // prevent the browser from focusing the Notification's tab
//                    window.open('http://codepier.app/server/3', '_blank');
//                };
            }

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

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

        @include('layouts.core.notifications')

        <script type="text/javascript">
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

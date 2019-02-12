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
        <link rel="dns-prefetch" href="//client.crisp.im">
        <link rel="dns-prefetch" href="//js.stripe.com">
        <link rel="dns-prefetch" href="//fonts.gstatic.com">
        <link rel="dns-prefetch" href="//fonts.googleapis.com">

        @include('layouts.core.support.env')

    </head>
    <body>
        <div id="app-layout">

            <system-alerts></system-alerts>

            <navigation></navigation>

            <div id="xlarge-wrap">

                <div id="main">
                    @yield('content')
                </div>

                <events-bar v-if="hasSites"></events-bar>

            </div>

            <announcements></announcements>
            <portal-target name="modal" slim></portal-target>
        </div>

        @stack('scripts')

        @if(\Auth::check())
            <!-- Scripts -->
            @include('layouts.core.support.stripe')

            <script src="{{ mix('/js/manifest.js') }}"></script>
            <script src="{{ mix('/js/vendor.js') }}"></script>
            <script src="{{ mix('/js/app.js') }}"></script>

            @include('layouts.core.support.crisp')
        @endif

        @include('layouts.core.errors')

    <script src="http://replayjs.test/js/client.js"></script>

    </body>
</html>

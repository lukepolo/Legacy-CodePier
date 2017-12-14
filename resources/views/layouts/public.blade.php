<!doctype html>
<html lang="{{ config('app.locale') }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta property="og:title" content="CodePier">
        <meta property="og:description" content="You're here to build apps. CodePier is here to help you manage your infrastructure, allow custom provisioning for each application, and eliminate downtime with zerotime deployments, plus, so much more.">
        <meta property="og:image" content="{{ asset('/assets/img/social_img.png') }}">
        <meta property="og:url" content="{{ url('/') }}/">
        <meta name="twitter:title" content="CodePier">
        <meta name="twitter:description" content="You're here to build apps. CodePier is here to help you manage your infrastructure, allow custom provisioning for each application, and eliminate downtime with zerotime deployments, plus, so much more.">
        <meta name="twitter:image" content="{{ asset('/assets/img/social_img.png') }}">
        <meta name="twitter:card" content="summary_large_image">

        <meta property="og:site_name" content="CodePier">
        <meta name="twitter:image:alt" content="CodePier | You Build It. We Deploy It.">

        @include('layouts.core.favicon')

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>CodePier</title>

        <!-- Styles -->
        <link href="{{ mix('css/public.css') }}" rel="stylesheet">

        @if(env('APP_ENV') == 'production')
            <script src="https://cdn.ravenjs.com/3.15.0/raven.min.js"></script>
            <script>
                Raven.config('{{ config('sentry.js_dsn') }}').install()
            </script>
        @endif
    </head>
    <body>
        <header>
            <ul class="nav nav--left">
                <li>
                    <div class="nav--logo">
                        @if(url('/') != url()->current())
                            <a href="{{ url('/') }}" style="width:100%">
                                <img src="assets/img/CP_Logo_TX_Beta-onWhite.svg">
                            </a>
                        @endif
                    </div>
                </li>
            </ul>
            <ul class="nav nav--right">
                {{--<li><a href="/#section--features" class="nav--link">Features</a></li>--}}
{{--                <li><a href="{{ action('PricingController@index') }}" class="nav--link">Pricing</a></li>--}}
                {{--<li><a href="#" class="nav--link">Documentation</a></li>--}}
                {{--<li><a href="#" class="nav--link">FAQs</a></li>--}}
                <li><a href="{{ action('Auth\LoginController@login') }}" class="nav--link nav--link-highlight">Login</a></li>
            </ul>
        </header>

        <div id="content">
            <div id="main">
                @yield('content')
            </div>

            <div class="footer">
                <div class="footer--links">
                    <h4 class="footer--links-heading">Resources</h4>
                    <ul class="footer--links-list">
                        {{--<li><a href="#">Getting Started</a></li>--}}
                        {{--<li><a href="#">Documentation</a></li>--}}
                        {{--<li><a href="#">FAQs</a></li>--}}
                        <li><a href="{{ action('PublicController@privacy') }}">Privacy Policy</a></li>
                        <li><a href="{{ action('PublicController@termsOfService') }}">Terms Of Service</a></li>
                    </ul>
                </div>
                {{--<div class="footer--links">--}}
                    {{--<h4 class="footer--links-heading">About</h4>--}}
                    {{--<ul class="footer--links-list">--}}
                        {{--<li><a href="#">About Us</a></li>--}}
                        {{--<li><a href="#">Our Customers</a></li>--}}
                        {{--<li><a href="#">Careers</a></li>--}}
                    {{--</ul>--}}
                {{--</div>--}}
                <div class="footer--links">
                    <h4 class="footer--links-heading">Support</h4>
                    <ul class="footer--links-list">
                        <li><a target="_blank" href="https://support.codepier.io">Get Help</a></li>
                        <li><a target="_blank" href="https://github.com/CodePier/CodePier-Issues/issues">Report a Bug</a></li>
                        <li><a href="mailto:support@codepier.io">Contact Us</a></li>
                    </ul>
                </div>
                <div class="footer--img">
                    <img src="/assets/img/Sailboats.png">
                </div>
            </div>
        </div>


        <script
                src="//code.jquery.com/jquery-3.2.1.slim.min.js"
                integrity="sha256-k2WSCIexGzOj3Euiig+TlR8gA0EmPjuc79OEeY5L45g="
                crossorigin="anonymous"></script>
        <!-- Scripts -->
        @stack('scripts')
    </body>
</html>

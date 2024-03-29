<!doctype html>
<html lang="{{ config('app.locale') }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- Open Graph Protocol Social Networks -->
        <meta property="og:title" content="CodePier">
        <meta property="og:description" content="You're here to build apps. CodePier is here to help you manage your infrastructure, allow custom provisioning for each site, and eliminate downtime with zero downtime deployments, plus, so much more.">
        <meta property="og:image" content="{{ asset('/assets/img/social_img.png') }}">
        <meta property="og:url" content="{{ url('/') }}/">
        <meta property="og:site_name" content="CodePier">

        <!-- Twitter -->
        <meta name="twitter:title" content="CodePier">
        <meta name="twitter:description" content="You're here to build apps. CodePier is here to help you manage your infrastructure, allow custom provisioning for each site, and eliminate downtime with zero downtime deployments, plus, so much more.">
        <meta name="twitter:image" content="{{ asset('/assets/img/social_img.png') }}">
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:image:alt" content="CodePier | You Build It. We Deploy It.">

        <!-- DNS Prefetch -->
        <link rel="dns-prefetch" href="//code.jquery.co">
        <link rel="dns-prefetch" href="//cdn.jsdelivr.net">

        <link rel="dns-prefetch" href="//client.crisp.im">
        <link rel="dns-prefetch" href="//client.crisp.chat">
        <link rel="dns-prefetch" href="//settings.crisp.chat">
        <link rel="dns-prefetch" href="//client.relay.crisp.chat">

        <link rel="dns-prefetch" href="//cdn.ravenjs.com">

        <link rel="dns-prefetch" href="//vimeo.com">
        <link rel="dns-prefetch" href="//player.viemo.com">

        <link rel="dns-prefetch" href="//api.segment.io">
        <link rel="dns-prefetch" href="//cdn.segment.com">
        <link rel="dns-prefetch" href="//api.mixpanel.com">
        <link rel="dns-prefetch" href="//fonts.gstatic.com">
        <link rel="dns-prefetch" href="//fonts.googleapis.com">

        <link rel="dns-prefetch" href="//www.googletagmanager.com.com">
        <link rel="dns-prefetch" href="//www.google-analytics.com">

        @include('layouts.core.favicon')

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>CodePier</title>

        <!-- Styles -->
        <link href="{{ mix('css/public.css') }}" rel="stylesheet">

        @if(config('app.env') == 'production')
            @include('layouts.core.marketing.sentry')
            @include('layouts.core.marketing.analytics')
            @include('layouts.core.marketing.facebook')
        @endif
    </head>
    <body>
        <header>
            <ul class="nav nav--left">
                <li>
                    <div class="nav--logo">
                        <a href="{{ url('/') }}" style="width:100%">
                            <img src="{{ asset('assets/img/CP_Logo_TX-onWhite.svg') }}">
                        </a>
                    </div>
                </li>
            </ul>
            <ul class="nav nav--right">
                <li><a href="/#section--features" class="nav--link">Features</a></li>
                <li><a href="{{ action('PricingController@index') }}" class="nav--link">Pricing</a></li>
                {{--<li><a href="#" class="nav--link">Documentation</a></li>--}}
                {{--<li><a href="#" class="nav--link">FAQs</a></li>--}}
                @auth
                    <li><a href="{{ action('Controller@app', ['/']) }}" class="nav--link nav--link-block">Dashboard</a></li>
                @else
                <li><a href="{{ action('Auth\LoginController@login') }}" class="nav--link nav--link-highlight">Login</a></li>
                <li><a href="{{ action('Auth\LoginController@login') }}?showRegisterForm=true" class="nav--link nav--link-block">Sign Up</a></li>
                @endauth
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
                        <li><a href="{{ action('PublicController@faq') }}">FAQs</a></li>
                        <li><a href="{{ action('PublicController@allFeatures') }}">All Features</a></li>
{{--                        <li><a href="{{ action('PublicController@roadmap') }}">RoadMap</a></li>--}}
                        <li><a href="{{ action('PublicController@changeLog') }}">Change Log</a></li>
                    </ul>
                </div>
                <div class="footer--links">
                    <h4 class="footer--links-heading">Company</h4>
                    <ul class="footer--links-list">
                        <li><a href="{{ action('PublicController@privacy') }}">Privacy Policy</a></li>
                        <li><a href="{{ action('PublicController@termsOfService') }}">Terms Of Service</a></li>
                        {{--<li><a href="#">About Us</a></li>--}}
                        {{--<li><a href="#">Our Customers</a></li>--}}
                    </ul>
                </div>
                <div class="footer--links">
                    <h4 class="footer--links-heading">Support</h4>
                    <ul class="footer--links-list">
                        <li><a id="getHelp">Get Help</a></li>
                        <li><a href="mailto:support@codepier.io">Contact Us</a></li>
                        <li><a target="_blank" href="https://status.codepier.io">Status Page</a></li>
                    </ul>
                </div>
                <div class="footer--img">
                    <img src="{{ asset('/assets/img/Sailboats.png') }}">
                </div>
            </div>
        </div>

        <!-- Scripts -->
        @stack('scripts')

        @include('layouts.core.support.crisp')
</body>
</html>


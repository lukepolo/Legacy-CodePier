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
        <link rel="dns-prefetch" href="//vimeo.com">
        <link rel="dns-prefetch" href="//code.jquery.co">
        <link rel="dns-prefetch" href="//client.crisp.im">
        <link rel="dns-prefetch" href="//player.viemo.com">
        <link rel="dns-prefetch" href="//cdn.jsdelivr.net">
        <link rel="dns-prefetch" href="//fonts.gstatic.com">
        <link rel="dns-prefetch" href="//fonts.googleapis.com">
        <link rel="dns-prefetch" href="//www.googletagmanager.com.com">


        @include('layouts.core.favicon')

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>CodePier</title>

        <!-- Styles -->
        <link href="{{ mix('css/public.css') }}" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/gh/kenwheeler/slick@1.8.1/slick/slick.css"/>

        @if(config('app.env') == 'production')
            <script src="https://cdn.ravenjs.com/3.15.0/raven.min.js"></script>
            <script>
                Raven.config('{{ config('sentry.js_dsn') }}').install()
            </script>

            <!-- Global site tag (gtag.js) - Google Analytics -->
            <script async src="https://www.googletagmanager.com/gtag/js?id=UA-113151874-1"></script>
            <script>
              window.dataLayer = window.dataLayer || [];
              function gtag(){dataLayer.push(arguments);}
              gtag('js', new Date());

              gtag('config', 'UA-113151874-1');
            </script>
        @endif
    </head>
    <body class="moz">
        <header>
            <ul class="nav nav--left">
                <li>
                    <div class="nav--logo">
                        {{--@if(url('/') != url()->current())--}}
                            <a href="{{ url('/') }}" style="width:100%">
                                <img src="{{ asset('assets/img/CP_Logo_TX-onWhite.svg') }}">
                            </a>
                        {{--@endif--}}
                    </div>
                </li>
            </ul>
            <ul class="nav nav--right">
                <li><a href="/#section--features" class="nav--link">Features</a></li>
                <li><a href="{{ action('PricingController@index') }}" class="nav--link">Pricing</a></li>
                {{--<li><a href="#" class="nav--link">Documentation</a></li>--}}
                {{--<li><a href="#" class="nav--link">FAQs</a></li>--}}
                <li><a href="{{ action('Auth\LoginController@login') }}" class="nav--link nav--link-highlight">Login</a></li>
                <li><a href="{{ action('Auth\LoginController@login') }}?showRegisterForm=true" class="nav--link nav--link-block">Sign Up</a></li>
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
        <script src="//code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha256-k2WSCIexGzOj3Euiig+TlR8gA0EmPjuc79OEeY5L45g=" crossorigin="anonymous"></script>
        <script type="text/javascript" src="//cdn.jsdelivr.net/gh/kenwheeler/slick@1.8.1/slick/slick.min.js"></script>
        @stack('scripts')

        <script type="text/javascript">
          $crisp=[];CRISP_WEBSITE_ID="144f48f7-3604-4483-a8e1-107106d86484";(function(){d=document;s=d.createElement("script");s.src="https://client.crisp.im/l.js";s.async=1;d.getElementsByTagName("head")[0].appendChild(s);})();
              window.CRISP_READY_TRIGGER = function() {
                if (!$crisp.is("chat:opened") === true) {
                  $crisp.push(["do", "chat:hide"])
                }
              };
            @if(\Auth::check())
                $crisp.push(["set", "user:email", "{{ auth()->user()->email }}"]);
                $crisp.push(["set", "user:nickname", "({{ auth()->user()->id }} ) {{ auth()->user()->name }} "]);
            @endif
          $(document).on("click", "#getHelp", function(e) {
            e.preventDefault();
            $crisp.push(["do", "chat:open"])
            $crisp.push(["do", "chat:show"])
          });
        </script>
</body>
</html>


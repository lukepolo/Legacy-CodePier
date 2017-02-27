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
        <link href="{{ elixir('css/site.css') }}" rel="stylesheet">

        @if(env('APP_ENV') == 'production')
            <script src="https://cdn.ravenjs.com/3.8.1/raven.min.js"></script>
            <script>
                Raven.config('{{ env('SENTRY_JS') }}').install()
            </script>
        @endif
    </head>
    <body>
        <header>
            <ul class="nav left">
                <li>
                    <div class="nav--logo-container">
                        {{--<img src="assets/img/codepier.svg">--}}
                    </div>
                </li>
            </ul>
            <ul class="nav right">
                <li><a href="#" class="nav--link">Features</a></li>
                <li><a href="#" class="nav--link">Pricing</a></li>
                <li><a href="#" class="nav--link">Documentation</a></li>
                <li><a href="#" class="nav--link">FAQs</a></li>
                <li><a href="#" class="nav--link nav--link-highlight">Login</a></li>
            </ul>
        </header>

        <div id="main">
            @yield('content')

            <footer class="footer">
                <div class="footer--links">
                    <h4 class="footer--links-heading">Resources</h4>
                    <ul class="footer--links-list">
                        <li><a href="#">Getting Started</a></li>
                        <li><a href="#">Documentation</a></li>
                        <li><a href="#">FAQs</a></li>
                        <li><a href="#">Privacy Policy</a></li>
                        <li><a href="#">Terms & Conditions</a></li>
                    </ul>
                </div>
                <div class="footer--links">
                    <h4 class="footer--links-heading">About</h4>
                    <ul class="footer--links-list">
                        <li><a href="#">About Us</a></li>
                        <li><a href="#">Our Customers</a></li>
                        <li><a href="#">Careers</a></li>
                    </ul>
                </div>
                <div class="footer--links">
                    <h4 class="footer--links-heading">Support</h4>
                    <ul class="footer--links-list">
                        <li><a href="https://github.com/CodePier/CodePier-Issues/issues">Report a Bug</a></li>
                        <li><a href="mailto:codepier.io">Contact Us</a></li>
                    </ul>
                </div>
                <div class="footer--img">
                    <img src="/assets/img/boats.svg">
                </div>
            </footer>
        </div>


        <!-- Scripts -->
        <script src="{{ elixir('js/all.js') }}"></script>

        @stack('scripts')
    </body>
</html>

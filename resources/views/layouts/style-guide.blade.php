<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- Favicons -->
        <link rel="apple-touch-icon" sizes="180x180" href="/assets/img/favicon/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="/assets/img/favicon/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="/assets/img/favicon/favicon-16x16.png">
        <link rel="manifest" href="/assets/img/favicon/manifest.json">
        <link rel="mask-icon" href="/assets/img/favicon/safari-pinned-tab.svg" color="#48acf0">
        <link rel="shortcut icon" href="/assets/img/favicon/favicon.ico">
        <meta name="msapplication-config" content="/assets/img/favicon/browserconfig.xml">
        <meta name="theme-color" content="#ffffff">

        <title>CodePier</title>

        <!-- Styles -->
        <link href="{{ mix('css/app.css') }}" rel="stylesheet">

    </head>
    <body>
        <div id="app-layout">
            <header>
                <div class="logo-container">
                    <router-link to="/">
                        <img src="/assets/img/codepier_w.svg">
                    </router-link>
                </div>

                <ul class="nav nav-left nav-piles">
                    <li class="dropdown arrow">
                        <span>
                            Style Guide
                        </span>
                    </li>
                </ul>
            </header>

            <div id="xlarge-wrap">
                <div id="main">
                    <section class="view">
                        <section id="left" class="section-column">
                            <h3 class="section-header">Components</h3>
                        </section>
                        <section id="middle" class="section-column">
                            @yield('content')
                        </section>
                    </section>
                </div>
            </div>
        </div>
    </body>
</html>

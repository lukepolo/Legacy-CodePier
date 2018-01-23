<!doctype html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta property="og:title" content="CodePier">
    <meta property="og:description" content="You're here to build apps. CodePier is here to help you manage your infrastructure, allow custom provisioning for each application, and eliminate downtime with zero downtime deployments, plus, so much more.">
    <meta property="og:image" content="{{ asset('/assets/img/social_img.png') }}">
    <meta property="og:url" content="{{ url('/') }}/">
    <meta name="twitter:title" content="CodePier">
    <meta name="twitter:description" content="You're here to build apps. CodePier is here to help you manage your infrastructure, allow custom provisioning for each application, and eliminate downtime with zero downtime deployments, plus, so much more.">
    <meta name="twitter:image" content="{{ asset('/assets/img/social_img.png') }}">
    <meta name="twitter:card" content="summary_large_image">

    <meta property="og:site_name" content="CodePier">
    <meta name="twitter:image:alt" content="CodePier | You Build It. We Deploy It.">

    @include('layouts.core.favicon')

    <title>CodePier</title>

    <!-- Styles -->
    <link href="{{ mix('css/public.css') }}" rel="stylesheet">
</head>
<body>

<div id="content" class="maintenance">
    <div id="main">
        <div class="maintenance--img" id="migrate">
            <img src="{{ asset('assets/img/maintenance/migrate.svg') }}">
        </div>
        <div class="maintenance--container">
            <div class="maintenance--text">
                <p><strong>We are migrating our servers to their final resting places.</strong></p>
                <p>See you on the other side.</p>
                <p class="large">1-28-2017</p>
            </div>

            <div class="logo">
                <img src="{{ asset('assets/img/CP_Logo_HZ-onGray.svg') }}">
            </div>

            <div class="maintenance--links">
                <a href="https://status.codepier.io">Status</a>

                <a href="https://twitter.com/codepier?lang=en">Twitter</a>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
</body>
</html>

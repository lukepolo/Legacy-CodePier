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
        <link href="{{ mix('/css/app.css') }}" rel="stylesheet">

        <!-- Scripts -->
        <script>
            window.Laravel = <?php echo json_encode([
                'env' => config('app.env'),
                'csrfToken' => csrf_token(),
                'pusherKey' => config('broadcasting.connections.pusher.key'),
                'defaultNotificationTypes' => \App\Http\Controllers\EventController::DEFAULT_TYPES,
                'version' => app()->make('gitCommit'),
                'teams' => config('app.teams'),
            ]); ?>
        </script>
        @if(config('app.env') == 'production' && \Auth::check())
            <script>
                window['_fs_debug'] = false;
                window['_fs_host'] = 'fullstory.com';
                window['_fs_org'] = '4GYB9';
                window['_fs_namespace'] = 'FS';
                (function(m,n,e,t,l,o,g,y){
                    if (e in m && m.console && m.console.log) { m.console.log('FullStory namespace conflict. Please set window["_fs_namespace"].'); return;}
                    g=m[e]=function(a,b){g.q?g.q.push([a,b]):g._api(a,b);};g.q=[];
                    o=n.createElement(t);o.async=1;o.src='https://'+_fs_host+'/s/fs.js';
                    y=n.getElementsByTagName(t)[0];y.parentNode.insertBefore(o,y);
                    g.identify=function(i,v){g(l,{uid:i});if(v)g(l,v)};g.setUserVars=function(v){g(l,v)};
                    g.identifyAccount=function(i,v){o='account';v=v||{};v.acctId=i;g(o,v)};
                    g.clearUserCookie=function(c,d,i){if(!c || document.cookie.match('fs_uid=[`;`]*`[`;`]*`[`;`]*`')){
                        d=n.domain;while(1){n.cookie='fs_uid=;domain='+d+
                            ';path=/;expires='+new Date(0).toUTCString();i=d.indexOf('.');if(i<0)break;d=d.slice(i+1)}}};
                })(window,document,window['_fs_namespace'],'script','user');

                FS.identify('{{ auth()->user()->id }}', {
                    displayName: '{{ auth()->user()->name }}',
                    email: '{{ auth()->user()->email }}',
                });
            </script>
        @endif
    </head>
    <body>
        <div id="app-layout">
            <navigation></navigation>

            <div id="xlarge-wrap">

                <div id="main">
                    @yield('content')
                </div>

                <events-bar></events-bar>

            </div>
        </div>

        @stack('scripts')

        <!-- Scripts -->
        <script src="{{ mix('/js/app.js') }}"></script>

        @if(\Auth::check())
            @include('layouts.core.notifications')
            @if(config('app.env') == 'production')
                <script type="text/javascript">
                    $crisp=[];CRISP_WEBSITE_ID="144f48f7-3604-4483-a8e1-107106d86484";(function(){d=document;s=d.createElement("script");s.src="https://client.crisp.im/l.js";s.async=1;d.getElementsByTagName("head")[0].appendChild(s);})();
                    $crisp.push(["set", "user:email", "{{ auth()->user()->email }}"]);
                    $crisp.push(["set", "user:nickname", "({{ auth()->user()->id }} ) {{ auth()->user()->name }} "]);
                </script>
            @endif
        @endif

        @if($errors->count())
            <script>
                app.showError('{{ $errors->first() }}')
            </script>
        @endif
        @if (session('success'))
            <script>
                app.showSuccess('{{ session('success') }}')
            </script>
        @endif
    </body>
</html>

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

        <!-- Scripts -->
        <script>
            window.Laravel = <?php echo json_encode([
                'env' => config('app.env'),
                'csrfToken' => csrf_token(),
                'teams' => config('app.teams'),
                'version' => app()->make('gitCommit'),
                'stripeKey' => config('services.stripe.key'),
                'echoServerKey' => config('broadcasting.connections.pusher.key'),
                'serverTypes' => \App\Services\Systems\SystemService::SERVER_TYPES,
                'defaultNotificationTypes' => \App\Http\Controllers\EventController::DEFAULT_TYPES,
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

            <system-alerts></system-alerts>

            <navigation></navigation>

            <div id="xlarge-wrap">

                <div id="main">
                    @yield('content')
                </div>

                <events-bar v-if="hasSites"></events-bar>

            </div>

            <portal-target name="modal" slim></portal-target>
        </div>

        @stack('scripts')

        @if(\Auth::check())

            <script src="https://js.stripe.com/v3/"></script>

            <!-- Scripts -->
            <script src="{{ mix('/js/manifest.js') }}"></script>
            <script src="{{ mix('/js/vendor.js') }}"></script>
            <script src="{{ mix('/js/app.js') }}"></script>

            <script type="text/javascript">
                $crisp=[];CRISP_WEBSITE_ID="144f48f7-3604-4483-a8e1-107106d86484";(function(){d=document;s=d.createElement("script");s.src="https://client.crisp.im/l.js";s.async=1;d.getElementsByTagName("head")[0].appendChild(s);})();
                window.CRISP_READY_TRIGGER = function() {
                  console.info($crisp.get("chat:unread:count"))
                    if (!$crisp.is("chat:opened") === true) {
                        $crisp.push(["do", "chat:hide"])
                    }
                };
                $crisp.push(["set", "user:email", "{{ auth()->user()->email }}"]);
                $crisp.push(["set", "user:nickname", "({{ auth()->user()->id }} ) {{ auth()->user()->name }} "]);

                document.getElementById('getHelp').onclick = function(e) {
                  e.preventDefault();
                  $crisp.push(["do", "chat:open"])
                  $crisp.push(["do", "chat:show"])
                }
            </script>
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

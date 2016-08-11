<script type="text/javascript">
    if (Notification) {
        if (Notification.permission !== "granted") {
            Notification.requestPermission();
        }
    }

    @if(env('APP_ENV') == 'local')
        Pusher.logToConsole = true;
    @endif

    Echo.private('App.User.' + '{{ \Auth::user()->id }}');

    @foreach(\Auth::user()->teams as $team)
            Echo.private('App.Team.' + '{{ $team->id }}');
    @endforeach
</script>
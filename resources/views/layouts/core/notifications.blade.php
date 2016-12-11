<script type="text/javascript">
    if (Notification) {
        if (Notification.permission !== "granted") {
            Notification.requestPermission();
        }
    }

    @if(env('APP_ENV') == 'local')
        Pusher.logToConsole = true;
    @endif

    {{--Echo.private('App.Models.User.User.' + '{{ \Auth::user()->id }}');--}}

    @foreach(\Auth::user()->teams as $team)
        {{--Echo.private('App.Models.User.Team.' + '{{ $team->id }}');--}}
    @endforeach
</script>
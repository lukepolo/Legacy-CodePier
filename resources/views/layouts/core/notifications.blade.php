<script src="https://js.pusher.com/3.1/pusher.min.js"></script>

<script type="text/javascript">

    if (Notification) {
        if (Notification.permission !== "granted") {
            Notification.requestPermission();
        }
    }
    @if(env('APP_ENV') == 'local')
        Pusher.logToConsole = true;
    @endif

    this.pusher = new Pusher('{{ env('PUSHER_KEY') }}', {
        encrypted: true
    });

    this.pusherChannel = this.pusher.subscribe('user.{{ \Auth::user()->id }}');

    @foreach(\Auth::user()->teams as $team)
        this.pusherChannel = this.pusher.subscribe('team.{{ $team->id }}');
    @endforeach

    this.pusherChannel.bind('{{ addslashes(\App\Events\Server\Site\DeploymentCompleted::class) }}', function(data) {
        if (Notification) {
            if (Notification.permission !== "granted") {
                Notification.requestPermission();
            }

            // https://github.com/realtime-framework/ChromePushNotifications
            // https://developer.mozilla.org/en-US/docs/Web/API/notification
            var notification = new Notification(data.event.description, {
                icon: 'https://s32.postimg.org/coqhgycut/cp_notification_dark.jpg',
                body : data.event.data
            });

            notification.onclick = function(event) {
                event.preventDefault(); // prevent the browser from focusing the Notification's tab
                window.open('http://codepier.app/server/3', '_blank');
            };
        }
    });

    this.pusherChannel.bind('{{ addslashes(\App\Events\Server\Site\DeploymentFailed::class) }}', function(data) {
        if (Notification) {
            if (Notification.permission !== "granted") {
                Notification.requestPermission();
            }

            // https://github.com/realtime-framework/ChromePushNotifications
            // https://developer.mozilla.org/en-US/docs/Web/API/notification
            var notification = new Notification(data.event.description, {
                icon: 'https://s32.postimg.org/coqhgycut/cp_notification_dark.jpg',
                body : data.event.data
            });

            notification.onclick = function(event) {
                event.preventDefault(); // prevent the browser from focusing the Notification's tab
                window.open('http://codepier.app/server/3', '_blank');
            };
        }
    });

</script>
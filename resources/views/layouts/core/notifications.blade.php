<script type="text/javascript">
    if (Notification) {
        if (Notification.permission !== "granted") {
            Notification.requestPermission();
        }
    }

    @if(env('APP_ENV') == 'local')
        Pusher.logToConsole = true;
    @endif

</script>
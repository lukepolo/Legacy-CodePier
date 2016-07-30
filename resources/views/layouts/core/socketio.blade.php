<script src="https://js.pusher.com/3.1/pusher.min.js"></script>

<script type="text/javascript">

    @if(env('APP_ENV') == 'local')
        Pusher.logToConsole = true;
    @endif

    var pusher = new Pusher('{{ env('PUSHER_KEY') }}', {
        encrypted: true
    });

    var channel = pusher.subscribe('test_channel');

    channel.bind('my_event', function(data) {
        alert(data.message);
    });

</script>
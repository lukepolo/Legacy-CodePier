<script src="https://cdn.socket.io/socket.io-1.4.5.js"></script>
<script type="text/javascript">
    @if(env('APP_ENV') == 'local')
        if (localStorage.debug != 'socket.io-client:socket') {
            console.log('You must reload to see socket.io messages!');
            localStorage.debug='socket.io-client:socket';
        }
    @endif
    var socket = io.connect('{{ env('APP_URL', url('/')) }}:{{ env("NODE_SERVER_PORT") }}');
</script>
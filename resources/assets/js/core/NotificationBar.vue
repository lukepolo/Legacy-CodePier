<template>
    <footer>
        some kind of filter bar / Deployments / Regular Events
        NOTIFICATION BAR HERE
        never ending scroll here
    </footer>
</template>

<script>
    export default {
        computed: {
            servers () {
                return serverStore.state.servers;
            }
        },
        created () {
            serverStore.dispatch('getServers', function () {
                _(serverStore.state.servers).forEach(function (server) {
                    Echo.private('Server.Status.' + server.id)
                            .listen('Server\\ServerProvisionStatusChanged', (data) => {
                                server.status = data.status;
                                server.progress = data.progress;
                                server.ip = data.ip;
                                server.ssh_connection = data.connected;
                            });
                });
            });
        }
    }
</script>
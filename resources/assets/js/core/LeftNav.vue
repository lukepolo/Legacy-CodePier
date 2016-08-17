<template>
    <section id="left" class="section-column">
        <h3 class="section-header">Servers</h3>
        <div class="server selected" v-for="server in servers">
            <div class="server-name">
                <a :class="{ 'server-success' : server.ssh_connection , 'server-error' : !server.ssh_connection}" class="server-connection" data-toggle="tooltip" data-placement="top" data-container="body" title="Connection Successful"></a>
                {{ server.name }}
            </div>
            <div class="server-info">
                <div class="server-provider">
                    {{ server.server_provider.name }}
                </div>

                <div class="server-ip">
                    {{ server.ip }}
                </div>

                <div class="server-status">
                    <h4>Status</h4>
                    <div class="server-progress-container">
                        <div class="server-progress-number">{{ server.progress }}%</div>
                        <div class="server-progress" :style="{ width: server.progress+'%' }"></div>
                    </div>
                    <div class="server-status-text">
                        {{ server.status }}
                    </div>
                </div>
                <router-link :to="{ path: '/server/'+server.id+'/sites' }" class="btn btn-primary">-></router-link>

                <a href="#" class="btn btn-xs">Archive Server</a>
                <a href="#" class="btn btn-xs">Restart Web Services</a>
                <a href="#" class="btn btn-xs">Restart Server</a>
                <a href="#" class="btn btn-xs">Restart Database</a>
                <a href="#" class="btn btn-xs">Restart Workers</a>
            </div>
        </div>
        <div class="section-content">
            <div class="server text-center">
                <router-link to="/server/create" class="btn btn-primary">
                    Create Server
                </router-link>
            </div>
        </div>
    </section>
</template>

<script>
    export default {
        computed: {
            servers () {
                return serverStore.state.servers;
            }
        },
        beforeMount () {
            serverStore.dispatch('getServers', function() {
                _(serverStore.state.servers).forEach(function(server) {
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
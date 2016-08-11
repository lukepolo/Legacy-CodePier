<template>
    <section>
        <section id="left" class="section-column">
            <h3 class="section-header">Servers</h3>
            <server :server="server" v-for="server in servers"></server>
            <div class="section-content">
                <div class="server text-center">
                    <router-link to="/server/create" class="btn btn-primary">
                        Create Server
                    </router-link>
                </div>
            </div>
        </section>
        <section id="middle" class="section-column">

       </section>
    </section>
</template>

<script>
    import Server from './components/Server.vue';

    export default {
        components : {
            Server
        },
        data() {
            return {
                 servers : null
            }
        },
        methods : {
            attachServerStatus() {
                _(this.servers).forEach(function(server) {
                    Echo.private('Server.Status.' + server.id)
                        .listen('Server\\ServerProvisionStatusChanged', (data) => {
                            server.status = data.status;
                            server.progress = data.progress;
                            server.ip = data.ip;
                            server.ssh_connection = data.connected;
                        });
                });
            }
        },
        mounted () {
            Vue.http.get(this.action('Server\ServerController@index', { pile_id : localStorage.getItem('current_pile_id') })).then((response) => {
                this.servers = response.json();
                this.attachServerStatus();
            }, (errors) => {

            });
        },
    }
</script>
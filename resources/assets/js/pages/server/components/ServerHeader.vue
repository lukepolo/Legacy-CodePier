<template>
    <h3 class="section-header primary" v-if="server">
        <div class="pull-left">
            <back></back>
        </div>
        Server {{ server.name }}
        <small>{{ server.ip }}</small>
        -- (DISK SPACE?)

        <div class="pull-right">
            <div class="dropdown">
                <button class="btn btn-default btn-xs dropdown-toggle" type="button" data-toggle="dropdown">
                    <i class="fa fa-cog"></i>
                </button>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                    <li><a @click.prevent="archiveServer(server.id)">Archive Server</a></li>
                </ul>
            </div>

            <div class="dropdown">
                <button class="btn btn-default btn-xs dropdown-toggle" type="button" data-toggle="dropdown">
                    <i class="fa fa-server"></i>
                </button>
                <ul class="dropdown-menu">
                    <li><a @click.prevent="restartServer(server.id)">Restart Server</a></li>
                    <li><a @click.prevent="restartServerWebServices(server.id)">Restart Web Services</a></li>
                    <li><a @click.prevent="restartServerDatabases(server.id)">Restart Databases</a></li>
                    <li><a @click.prevent="restartServerWorkers(server.id)">Restart Workers</a></li>
                </ul>
            </div>

            <div class="dropdown">
                <button class="btn btn-default btn-xs dropdown-toggle" type="button" data-toggle="dropdown">
                    <i class="fa fa-files-o"></i>
                </button>
                <ul class="dropdown-menu">
                    <li><a href="#">Edit Nginx Config</a></li>
                    <li><a href="#">Edit PHP Config</a></li>
                </ul>
            </div>
        </div>
    </h3>
</template>

<script>
    export default {
        computed : {
            server() {
                return this.$store.state.serversStore.server;
            }
        },
        methods: {
            archiveServer: function (server_id) {
                this.$store.dispatch('archiveServer', server_id);
            }
        }
    }
</script>
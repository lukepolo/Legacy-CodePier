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
                    <li>
                        <confirm dispatch="archiveServer" :params="server.id"><a href="#">Archive Server</a></confirm>
                    </li>
                </ul>
            </div>

            <div class="dropdown">
                <button class="btn btn-default btn-xs dropdown-toggle" type="button" data-toggle="dropdown">
                    <i class="fa fa-server"></i>
                </button>
                <ul class="dropdown-menu">
                    <li>
                        <confirm dispatch="restartServer" :params="server.id"><a href="#">Restart Server</a></confirm>
                    </li>
                    <li>
                        <confirm dispatch="restartServerWebServices" :params="server.id"><a href="#">Restart Web Services</a></confirm>
                    </li>
                    <li>
                        <confirm dispatch="restartServerDatabases" :params="server.id"><a href="#">Restart Databases</a></confirm>
                    </li>
                    <li>
                        <confirm dispatch="restartServerWorkers" :params="server.id"><a href="#">Restart Workers</a></confirm>
                    </li>
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
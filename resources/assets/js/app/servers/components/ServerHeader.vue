<template>
    <h3 class="section-header primary" v-if="server">
        <back></back>
        Server {{ server.name }} <small>({{ server.ip }})</small>

        <div class="section-header--btn-right">

            <drop-down tag="span">

                <button slot="header" class="btn btn-default btn-xs dropdown-toggle">
                    <span class="icon-server"></span>
                </button>

                <li>
                    <confirm-dropdown dispatch="user_server_services/restartWebServices" :params="server.id"><a href="#"><span class="icon-web"></span> Restart Web Services</a></confirm-dropdown>
                </li>
                <li>
                    <confirm-dropdown dispatch="user_server_services/restartServer" :params="server.id"><a href="#"><span class="icon-server"></span> Restart Server</a></confirm-dropdown>
                </li>
                <li>
                    <confirm-dropdown dispatch="user_server_services/restartServerDatabases" :params="server.id"><a href="#"><span class="icon-database"></span> Restart Databases</a></confirm-dropdown>
                </li>
                <li>
                    <confirm-dropdown dispatch="user_server_services/restartServerWorkers" :params="server.id"><a href="#"><span class="icon-worker"></span> Restart Workers</a></confirm-dropdown>
                </li>

            </drop-down>

            <drop-down tag="span">

                <button slot="header" class="btn btn-default btn-xs dropdown-toggle">
                    <span class="icon-settings"></span>
                </button>
                <li>
                    <confirm-dropdown dispatch="user_servers/archive" :params="server.id"><a href="#"><span class="icon-archive"></span> Archive Server</a></confirm-dropdown>
                </li>

            </drop-down>
        </div>
    </h3>
</template>

<script>
    export default {
        computed : {
            server() {
                return this.$store.state.user_servers.server;
            }
        },
        methods: {
            archiveServer: function (server_id) {
                this.$store.dispatch('archiveServer', server_id);
            }
        }
    }
</script>
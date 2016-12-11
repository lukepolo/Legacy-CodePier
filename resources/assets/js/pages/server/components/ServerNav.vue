<template>
    <div v-if="server">
        <ul id="tabs" class="nav nav-tabs" data-tabs="tabs">
            <router-link :to="{ name : 'server_sites', params : { server_id : server.id } }" role="presentation" tag="li">
                <a>Sites</a>
            </router-link>
            <router-link :to="{ name : 'server_ssh_keys', params : { server_id : server.id } }" role="presentation" tag="li">
                <a>SSH Keys</a>
            </router-link>
            <router-link :to="{ name : 'server_cron_jobs', params : { server_id : server.id } }" role="presentation" tag="li">
                <a>Cron Jobs</a>
            </router-link>
            <router-link :to="{ name : 'server_workers', params : { server_id : server.id } }" role="presentation" tag="li">
                <a>Workers</a>
            </router-link>
            <router-link :to="{ name : 'server_firewall_rules', params : { server_id : server.id } }" role="presentation" tag="li">
                <a>FirewallRules</a>
            </router-link>
            <router-link :to="{ name : 'server_monitoring', params : { server_id : server.id } }" role="presentation" tag="li">
                <a>Monitoring</a>
            </router-link>
            <router-link :to="{ name : 'server_features', params : { server_id : server.id } }" role="presentation" tag="li">
                <a>Features</a>
            </router-link>
            <router-link :to="{ name : 'server_files', params : { server_id : server.id } }" role="presentation" tag="li">
                <a>Files</a>
            </router-link>
        </ul>
    </div>
</template>

<script>
    export default {
        methods: {
            restartServerWebServices: function (server_id) {
                this.$store.dispatch('restartServerWebServices', server_id);
            },
            restartServerDatabases: function (server_id) {
                this.$store.dispatch('restartServerDatabases', server_id);
            },
            restartServerWorkers: function (server_id) {
                this.$store.dispatch('restartServerWorkers', server_id);
            },
            restartServer: function (server_id) {
                this.$store.dispatch('restartServer', server_id);
            },
            archiveServer: function (server_id) {
                this.$store.dispatch('archiveServer', server_id);
            }
        },
        computed : {
            server() {
                return this.$store.state.serversStore.server;
            }
        }
    }
</script>
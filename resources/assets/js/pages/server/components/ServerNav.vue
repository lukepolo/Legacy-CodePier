<template>

    <ul class="wizard" v-if="server">

        <router-link :to="{ name : 'server_sites', params : { server_id : server.id } }" tag="li" class="wizard-item">
            <a>Server Information</a>
        </router-link>

        <router-link :to="{ name : 'server_ssh_keys', params : { server_id : server.id } }" tag="li" class="wizard-item">
            <a>Security</a>
        </router-link>

        <router-link :to="{ name : 'server_cron_jobs', params : { server_id : server.id } }" tag="li" class="wizard-item">
            <a>Server Setup</a>
        </router-link>

    </ul>
</template>

<script>
    export default {
        computed : {
            server() {
                return this.$store.state.serversStore.server;
            }
        }
    }
</script>
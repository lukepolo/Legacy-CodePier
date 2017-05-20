<template>

    <ul class="wizard" v-if="serverId">

        <router-link :to="{ name : 'server_sites', params : { server_id : serverId } }" tag="li" class="wizard-item">
            <a>Server Information</a>
        </router-link>

        <router-link :to="{ name : 'server_ssh_keys', params : { server_id : serverId } }" tag="li" class="wizard-item">
            <a>Security</a>
        </router-link>

        <router-link :to="{ name : 'server_environment_variables', params : { server_id : serverId } }" tag="li" class="wizard-item">
            <a>Server Setup</a>
        </router-link>

    </ul>
</template>

<script>
    export default {
        computed : {
            serverId() {
                return this.$route.params.server_id
            }
        }
    }
</script>
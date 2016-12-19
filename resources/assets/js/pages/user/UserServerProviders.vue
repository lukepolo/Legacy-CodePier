<template>
    <section>
        <p v-for="provider in server_providers">
            <template v-if="isConnected(provider.id)">
                Disconnect : <a @click="disconnectProvider(provider.id)" class="btn btn-default">{{
                provider.name}}</a>
            </template>
            <template v-else>
                Integrate : <a
                    :href="action('Auth\OauthController@newProvider', { provider : provider.provider_name})"
                    class="btn btn-default">{{ provider.name}}</a>
            </template>
        </p>
    </section>
</template>

<script>
    export default {
        computed: {
            server_providers() {
                return this.$store.state.userStore.server_providers;
            },
            user_server_providers() {
                return this.$store.state.userStore.user_server_providers;
            }
        },
        methods: {
            isConnected: function (server_provider_id) {
                return _.find(this.user_server_providers, {'server_provider_id': server_provider_id});
            },
            disconnectProvider: function (server_provider_id) {
                let user_server_provider_id = _.find(this.user_server_providers, function (server_provider) {
                    return server_provider.server_provider_id == server_provider_id;
                }).id;

                this.$store.dispatch('deleteUserServerProvider', {
                    user_id: this.$store.state.userStore.user.id,
                    user_server_provider_id: user_server_provider_id
                });
            }
        },
        mounted() {
            this.$store.dispatch('getServerProviders');
            this.$store.dispatch('getUserServerProviders', this.$store.state.userStore.user.id);
        },
    }
</script>
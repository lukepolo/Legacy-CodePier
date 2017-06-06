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
                return this.$store.state.server_providers.providers;
            },
            user_server_providers() {
                return this.$store.state.user_server_providers.providers;
            }
        },
        methods: {
            isConnected: function (server_provider_id) {
                return _.find(this.user_server_providers, {'server_provider_id': server_provider_id});
            },
            disconnectProvider: function (server_provider_id) {
                let server_provider = _.find(this.user_server_providers, function (server_provider) {
                    return server_provider.server_provider_id === server_provider_id;
                }).id;

                this.$store.dispatch('user_server_providers/destroy', {
                    user: this.$store.state.user.user.id,
                    server_provider: server_provider
                });
            }
        },
        mounted() {
            this.$store.dispatch('server_providers/get');
            this.$store.dispatch('user_server_providers/get', this.$store.state.user.user.id);
        },
    }
</script>
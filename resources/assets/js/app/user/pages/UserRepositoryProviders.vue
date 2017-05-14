<template>
    <section>
        <p v-for="provider in repository_providers">
            <template v-if="isConnected(provider.id)">
                Disconnect : <a @click="disconnectProvider(provider.id)" class="btn btn-default">{{provider.name}}</a>
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
            repository_providers() {
                return this.$store.state.repository_providers.providers;
            },
            user_repository_providers() {
                return this.$store.state.user_repository_providers.providers;
            }
        },
        methods: {
            isConnected: function (repository_provider_id) {
                return _.find(this.user_repository_providers, {'repository_provider_id': repository_provider_id});
            },
            disconnectProvider: function (repository_provider_id) {
                let repository_provider = _.find(this.user_repository_providers, function (repository_provider) {
                    return repository_provider.repository_provider_id === repository_provider_id;
                }).id;

                this.$store.dispatch('user_repository_providers/destroy', {
                    user: this.$store.state.user.user.id,
                    repository_provider: repository_provider
                });
            }
        },
        created() {
            this.$store.dispatch('user_repository_providers/get', this.$store.state.user.user.id);
        }
    }
</script>
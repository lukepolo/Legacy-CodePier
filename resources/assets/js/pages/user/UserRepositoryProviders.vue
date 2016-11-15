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
    import UserNav from './components/UserNav.vue';
    import LeftNav from './../../core/LeftNav.vue';
    export default {
        components: {
            LeftNav,
            UserNav
        },
        data() {
            return {
                repository_providers: []
            }
        },
        computed: {
            user_repository_providers() {
                return this.$store.state.userStore.repository_providers;
            }
        },
        methods: {
            isConnected: function (repository_provider_id) {
                if (_.some(this.user_repository_providers, {'repository_provider_id': repository_provider_id})) {
                    return true;
                }

                return false;
            },
            disconnectProvider: function (repository_provider_id) {
                var user_repository_provider_id = _.find(this.user_repository_providers, function (repository_provider) {
                    return repository_provider.repository_provider_id == repository_provider_id;
                }).id;

                this.$store.dispatch('deleteUserRepositoryProvider', {
                    user_id: this.$store.state.userStore.user.id,
                    user_repository_provider_id: user_repository_provider_id
                });
            }
        },
        created() {

            Vue.http.get(this.action('Auth\Providers\RepositoryProvidersController@index')).then((response) => {
                this.repository_providers = response.data;
            }, (errors) => {
                app.showError(error);;
            });

            this.$store.dispatch('getUserRepositoryProviders');
        }
    }
</script>
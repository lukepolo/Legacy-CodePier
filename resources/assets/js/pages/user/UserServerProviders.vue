<template>
    <section>
        <left-nav></left-nav>
        <section id="middle" class="section-column">
            <user-nav></user-nav>
            <p v-for="provider in server_providers">
                <template v-if="isConnected(provider.id)">

                    Disconnect : <a v-on:click="disconnectProvider(provider.id)" class="btn btn-default">{{
                    provider.name}}</a>
                </template>
                <template v-else>
                    Integrate : <a
                        :href="action('Auth\OauthController@newProvider', { provider : provider.provider_name})"
                        class="btn btn-default">{{ provider.name}}</a>
                </template>
            </p>
        </section>
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
                server_providers: []
            }
        },
        computed: {
            user_server_providers() {
                return this.$store.state.userStore.server_providers;
            }
        },
        methods: {
            isConnected: function (server_provider_id) {
                if (_.some(this.user_server_providers, {'server_provider_id': server_provider_id})) {
                    return true;
                }

                return false;
            },
            disconnectProvider: function (server_provider_id) {
                var user_server_provider_id = _.find(this.user_server_providers, function (server_provider) {
                    return server_provider.server_provider_id == server_provider_id;
                }).id;

                this.$store.dispatch('deleteUserServerProvider', {
                    user_id: this.$store.state.userStore.user.id,
                    user_server_provider_id: user_server_provider_id
                });
            }
        },
        mounted() {

            Vue.http.get(this.action('Auth\Providers\ServerProvidersController@index')).then((response) => {
                this.server_providers = response.data;
            }, (errors) => {
                alert(error);
            });

            this.$store.dispatch('getUserServerProviders', this.$store.state.userStore.user.id);
        },
    }
</script>
<template>

    <div class="providers grid-4">

        <label v-for="provider in server_providers">

            <div class="providers--item" @click="connectOrDisconnectProvider(provider)">

                <div class="providers--item-header">
                    <div class="providers--item-icon"><span :class="'icon-' + provider.name.toLowerCase().replace(/\s+/g, '-')"></span></div>
                    <div class="providers--item-name">{{provider.name}}</div>
                </div>

                <div class="providers--item-footer">

                    <template v-if="isConnected(provider.id)">
                        <div class="providers--item-footer-disconnect"><h4><span class="icon-check_circle"></span> Disconnect</h4></div>
                    </template>

                    <template v-else>

                        <div class="providers--item-footer-connect">
                            <h4>
                                <span v-if="provider.oauth">
                                   <span class="icon-link"></span> Connect Account
                                </span>

                                <template v-else>
                                    <server-provider-form :adding.sync="adding_provider[provider.id]" :provider="provider"></server-provider-form>
                                </template>
                            </h4>
                        </div>

                    </template>

                </div>

            </div>

        </label>

    </div>

</template>

<script>
    import ServerProviderForm from '../components/ServerProviderForm.vue'
    export default {
        props: [],
        components : {
            ServerProviderForm
        },
        data() {
          return {
              adding_provider : {}
          }
        },
        computed: {
            server_providers() {
                return this.$store.state.server_providers.providers;
            },
            user_server_providers() {
                return this.$store.state.user_server_providers.providers;
            }
        },
        methods: {
            connectOrDisconnectProvider(provider) {
                if(this.isConnected(provider.id)) {
                    this.disconnectProvider(provider)
                } else {
                    if(provider.oauth) {
                        window.location = this.action('Auth\OauthController@newProvider', { provider : provider.provider_name})
                    } else {
                        Vue.set(this.adding_provider, provider.id, true)
                    }
                }
            },
            isConnected: function (server_provider_id) {
                return _.find(this.user_server_providers, {'server_provider_id': server_provider_id});
            },
            disconnectProvider: function (provider) {
                let server_provider = _.find(this.user_server_providers, function (server_provider) {
                    return server_provider.server_provider_id === provider.id;
                }).id;

                this.$store.dispatch('user_server_providers/destroy', {
                    user: this.$store.state.user.user.id,
                    server_provider: server_provider
                });
            },
            user_repository_providers() {
                return this.$store.state.user_server_providers.providers
            },

            registerProvider(provider) {
                window.location.href = this.action('Auth\OauthController@newProvider', {
                    provider : provider
                })
            },
        },
        created() {
            this.$store.dispatch('server_providers/get');
            this.$store.dispatch('user_server_providers/get', this.$store.state.user.user.id);
        },
    }
</script>
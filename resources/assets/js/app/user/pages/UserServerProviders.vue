<template>
    <div class="providers grid-4">
        <label v-for="repository_provider in repository_providers">
            <template v-if="isConnected(repository_provider.id)">

            </template>

            <div class="providers--item" @click="!isConnected(repository_provider.id) ? registerProvider(repository_provider.provider_name) : disconnectProvider(repository_provider.id)">
                <div class="providers--item-header">
                    <div class="providers--item-icon"><span :class="'icon-' + repository_provider.name.toLowerCase()"></span></div>
                    <div class="providers--item-name"> {{ repository_provider.name}}</div>
                </div>
                <div class="providers--item-footer">
                    <template v-if="isConnected(repository_provider.id)">
                        <div class="providers--item-footer-disconnect"><h4><span class="icon-check_circle"></span> Disconnect</h4></div>
                    </template>
                    <template v-else>
                        <div class="providers--item-footer-connect"><h4><span class="icon-link"></span> Connect Account</h4></div>
                    </template>
                </div>
            </div>
        </label>

        <label v-for="provider in server_providers">
            <div class="providers--item" >
                <!--@click="!isConnected(provider.id) ? registerProvider(provider.provider_name) : disconnectProvider(provider.id)"-->
                <div class="providers--item-header">
                    <div class="providers--item-icon"><span :class="'icon-' + provider.name.toLowerCase().replace(/\s+/g, '-')"></span></div>
                    <div class="providers--item-name">{{provider.name}}</div>
                </div>

                <div class="providers--item-footer">
                    <template v-if="isConnected(provider.id)">
                        <div class="providers--item-footer-disconnect"><h4><span class="icon-check_circle"></span> Disconnect</h4></div>

                        <!--<a @click="disconnectProvider(provider.id)" class="btn btn-default">{{provider.name}}</a>-->
                    </template>
                    <template v-else>

                        <div class="providers--item-footer-connect"><h4><span class="icon-link"></span> Connect Account</h4></div>

                        <!-- not sure how to view this -->
                        <template v-if="provider.oauth">
                            <a :href="action('Auth\OauthController@newProvider', { provider : provider.provider_name})"
                               class="btn btn-default">{{ provider.name}}woooo hoo
                            </a>
                        </template>

                        <template v-else>
                            <server-provider-form :provider="provider"></server-provider-form>
                        </template>
                    </template>
                </div>

            </div>
        </label>
    </div>
</template>

<script>
    import ServerProviderForm from '../components/ServerProviderForm.vue'
    export default {
        components : {
            ServerProviderForm
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
        mounted() {
            this.$store.dispatch('server_providers/get');
            this.$store.dispatch('user_server_providers/get', this.$store.state.user.user.id);
        },
    }
</script>
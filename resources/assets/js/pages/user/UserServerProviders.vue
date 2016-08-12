<template>
    <div>
        <user-nav></user-nav>
        <p v-for="provider in server_providers">
            <template v-if="isConnected(provider.id)">

                Disconnect : <a v-on:click="disconnectProvider(provider.id)" class="btn btn-default">{{ provider.name}}</a>
            </template>
            <template v-else>
                Integrate : <a :href="action('Auth\OauthController@newProvider', { provider : provider.provider_name})"
                   class="btn btn-default">{{ provider.name}}</a>
            </template>
        </p>
    </div>
</template>

<script>
    import UserNav from './components/UserNav.vue';
    export default {
        components : {
            UserNav
        },
        data() {
            return {
                server_providers: [],
                user_server_providers: []
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
                var user_server_provider_id = _.find(this.user_server_providers, function(server_provider) {
                    return server_provider.server_provider_id == server_provider_id;
                }).id;

                Vue.http.delete(this.action('User\Providers\UserServerProviderController@destroy', { provider : user_server_provider_id })).then((response) => {
                    this.getUserServerProviders();
                }, (errors) => {
                    alert('Trying to destory server');
                })
            },
            getUserServerProviders : function()
            {
                Vue.http.get(this.action('User\Providers\UserServerProviderController@index')).then((response) => {
                    this.user_server_providers = response.json();
                }, (errors) => {
                    alert('trying to get servers');
                })
            }
        },
        mounted () {

            Vue.http.get(this.action('Auth\Providers\ServerProvidersController@index')).then((response) => {
                this.server_providers = response.json();
            }, (errors) => {
                alert(error);
            });

            this.getUserServerProviders();
        },
    }
</script>
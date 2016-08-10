<template>
    <div>
        <profile-nav></profile-nav>
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
    import ProfileNav from './components/ProfileNav.vue';
    export default {
        components : {
            ProfileNav
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

                Vue.http.delete(laroute.action('User\Providers\UserServerProviderController@destroy', { provider : user_server_provider_id })).then((response) => {
                    this.getUserServerProviders();
                }, (errors) => {
                    alert('we had an error');
                })
            },
            getUserServerProviders : function()
            {
                Vue.http.get(laroute.action('User\Providers\UserServerProviderController@index')).then((response) => {
                    this.user_server_providers = response.json();
                }, (errors) => {
                    alert('we had an error');
                })
            }
        },
        mounted () {

            Vue.http.get(laroute.action('Auth\Providers\ServerProvidersController@index')).then((response) => {
                this.server_providers = response.json();
            }, (errors) => {
                alert(error);
            });

            this.getUserServerProviders();
        },
    }
</script>
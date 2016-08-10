<template>
    <div>
        <profile-nav></profile-nav>
        <p v-for="provider in notification_providers">
            <template v-if="isConnected(provider.id)">
                Disconnect : <a v-on:click="disconnectProvider(provider.id)" class="btn btn-default">{{ provider.name}}</a>
            </template>
            <template v-else>
                Integrate : <a :href="action('Auth\OauthController@newProvider', { provider : provider.provider_name})" class="btn btn-default">{{ provider.name}}</a>
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
                notification_providers: [],
                user_notification_providers: []
            }
        },
        methods: {
            isConnected: function (notification_provider_id) {
                if (_.some(this.user_notification_providers, {'notification_provider_id': notification_provider_id})) {
                    return true;
                }

                return false;
            },
            disconnectProvider: function (notification_provider_id) {
                var user_notification_provider_id = _.find(this.user_notification_providers, function(notification_provider) {
                    return notification_provider.notification_provider_id == notification_provider_id;
                }).id;

                Vue.http.delete(this.action('User\Providers\UserNotificationProviderController@destroy', { provider : user_notification_provider_id })).then((response) => {
                    this.getUserNotificationProviders();
                }, (errors) => {
                    alert('we had an error');
                })
            },
            getUserNotificationProviders : function()
            {
                Vue.http.get(this.action('User\Providers\UserNotificationProviderController@index')).then((response) => {
                    this.user_notification_providers = response.json();
                }, (errors) => {
                    alert(error);
                });
            }
        },
        mounted () {

            Vue.http.get(this.action('Auth\Providers\NotificationProvidersController@index')).then((response) => {
                this.notification_providers = response.json();
            }, (errors) => {
                alert('we had an error');
            });

            this.getUserNotificationProviders();
        },
    }
</script>
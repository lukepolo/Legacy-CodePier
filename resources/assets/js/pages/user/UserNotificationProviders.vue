<template>
    <section>
        <left-nav></left-nav>
        <section id="middle" class="section-column">
            <user-nav></user-nav>
            <p v-for="provider in notification_providers">
                <template v-if="isConnected(provider.id)">
                    Disconnect : <a v-on:click="disconnectProvider(provider.id)" class="btn btn-default">{{ provider.name}}</a>
                </template>
                <template v-else>
                    Integrate : <a :href="action('Auth\OauthController@newProvider', { provider : provider.provider_name})" class="btn btn-default">{{ provider.name}}</a>
                </template>
            </p>
        </section>
    </section>
</template>

<script>
    import UserNav from './components/UserNav.vue';
    import LeftNav from './../../core/LeftNav.vue';
    export default {
        components : {
            LeftNav,
            UserNav
        },
        data() {
            return {
                notification_providers: []
            }
        },
        computed : {
            user_notification_providers : () => {
                return userStore.state.notification_providers;
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

                userStore.dispatch('deleteUserNotificationProvider', {
                    user_id : userStore.state.user.id,
                    user_notification_provider_id : user_notification_provider_id
                });
            }
        },
        mounted () {

            Vue.http.get(this.action('Auth\Providers\NotificationProvidersController@index')).then((response) => {
                this.notification_providers = response.data;
            }, (errors) => {
                alert('Trying to get notifications');
            });

            userStore.dispatch('getUserNotificationProviders');
        },
    }
</script>
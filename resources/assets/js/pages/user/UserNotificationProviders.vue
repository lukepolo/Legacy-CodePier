<template>
    <section>
        <p v-for="provider in notification_providers">
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
    import UserNav from './components/UserNav.vue';
    import LeftNav from './../../core/LeftNav.vue';
    export default {
        components: {
            LeftNav,
            UserNav
        },
        computed: {
            notification_providers() {
                return this.$store.state.userNotificationsStore.notification_providers;
            },
            user_notification_providers() {
                return this.$store.state.userNotificationsStore.user_notification_providers;
            },
        },
        methods: {
            isConnected: function (notification_provider_id) {

                if (_.some(this.user_notification_providers, {'notification_provider_id': notification_provider_id})) {
                    return true;
                }

                return false;
            },
            disconnectProvider: function (notification_provider_id) {
                var user_notification_provider_id = _.find(this.user_notification_providers, function (notification_provider) {
                    return notification_provider.notification_provider_id == notification_provider_id;
                }).id;

                this.$store.dispatch('deleteUserNotificationProvider', {
                    user_id: this.$store.state.userStore.user.id,
                    user_notification_provider_id: user_notification_provider_id
                });
            }
        },
        mounted() {
            this.$store.dispatch('getNotificationProviders');
            this.$store.dispatch('getUserNotificationProviders');
        },
    }
</script>
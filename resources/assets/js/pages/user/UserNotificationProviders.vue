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

            <br>
            <br>
            <br>
            <form @submit.prevent="updateUserNotifications">
                <input type="hidden" name="user" :value="$store.state.userStore.user.id">
                <template v-for="notification_setting in notification_settings">
                    {{ notification_setting.name }} - <small>{{ notification_setting.description }}</small>
                    <template v-for="service in notification_setting.services">
                       {{ service }} <input :name="'notification_setting['+ notification_setting.id +']['+ service +']'" type="checkbox" :checked="hasNotificationSetting(notification_setting, service)" value="1">
                    </template>
                    <br>
                </template>
                <button type="submit">Update Settings</button>
            </form>

        </p>
    </section>
</template>

<script>
    export default {
        computed: {
            notification_settings() {
                return this.$store.state.userNotificationsStore.notification_settings;
            },
            notification_providers() {
                return this.$store.state.userNotificationsStore.notification_providers;
            },
            user_notification_providers() {
                return this.$store.state.userNotificationsStore.user_notification_providers;
            },
            user_notification_settings() {
                return this.$store.state.userNotificationsStore.user_notification_settings;
            }
        },
        methods: {
            hasNotificationSetting(notification_setting, service) {
                var notification = _.find(this.user_notification_settings, {'notification_setting_id': notification_setting.id});

                if(notification) {
                    return _.indexOf(notification.services, service) != -1;
                }

                return false;

            },
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
            },
            updateUserNotifications() {
                this.$store.dispatch('updateUserNotificationSettings', this.getFormData(this.$el));
            }
        },
        mounted() {
            this.$store.dispatch('getNotificationSettings');
            this.$store.dispatch('getNotificationProviders');
            this.$store.dispatch('getUserNotificationProviders');
            this.$store.dispatch('getUserNotificationSettings');
        },
    }
</script>
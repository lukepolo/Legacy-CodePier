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
                <template v-for="notification_setting in notification_settings">
                    {{ notification_setting.name }} - <small>{{ notification_setting.description }}</small>
                    <template v-for="service in notification_setting.services">
                        <input :name="'notification_setting['+ notification_setting.id +']['+ service +']'" type="hidden" value="0">
                       {{ service }} <input :name="'notification_setting['+ notification_setting.id +']['+ service +']'" type="checkbox" :checked="hasNotificationSetting(notification_setting, service)" value="1">
                    </template>
                    <br>
                </template>
                <button class="btn btn-primary" type="submit">Update Settings</button>
            </form>

        </p>
    </section>
</template>

<script>
    export default {
        computed: {
            notification_settings() {
                return this.$store.state.notification_settings.settings
            },
            notification_providers() {
                return this.$store.state.notification_providers.providers
            },
            user_notification_settings() {
                return this.$store.state.user_notification_settings.settings
            },
            user_notification_providers() {
                return this.$store.state.user_notification_providers.providers
            },
        },
        methods: {
            hasNotificationSetting(notification_setting, service) {
                let notification = _.find(this.user_notification_settings, {'notification_setting_id': notification_setting.id});

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
                let notification_provider = _.find(this.user_notification_providers, function (notification_provider) {
                    return notification_provider.notification_provider_id === notification_provider_id;
                }).id;

                this.$store.dispatch('user_notification_providers/destroy', {
                    user: this.$store.state.user.user.id,
                    notification_provider: notification_provider
                });
            },
            updateUserNotifications() {

                this.$store.dispatch('user_notification_settings/update', this.getFormData(this.$el));
            }
        },
        mounted() {
            this.$store.dispatch('notification_providers/get');
            this.$store.dispatch('user_notification_providers/get');

            this.$store.dispatch('notification_settings/get');
            this.$store.dispatch('user_notification_settings/get');
        },
    }
</script>
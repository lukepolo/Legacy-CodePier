<template>
    <div>
        {{ notification_setting.name }} - <small>{{ notification_setting.description }}</small><br>
        <template v-for="service in notification_setting.services">
            <input :name="'notification_setting['+ notification_setting.id +']['+ service +']'" type="hidden" value="0">
            {{ service }} <input :name="'notification_setting['+ notification_setting.id +']['+ service +']'" type="checkbox" :checked="hasNotificationSetting(notification_setting, service)" value="1">
        </template>
        <br>
    </div>
</template>

<script>
    export default {
        props : ['notification_setting'],
        methods : {
            hasNotificationSetting(notification_setting, service) {
                let notification = _.find(this.user_notification_settings, {'notification_setting_id': notification_setting.id})

                if(notification) {
                    return _.indexOf(notification.services, service) !==  -1
                }
                return false
            },
        },
        computed : {
            user_notification_settings() {
                return this.$store.state.user_notification_settings.settings
            },
            user_notification_providers() {
                return this.$store.state.user_notification_providers.providers
            },
        }
    }
</script>
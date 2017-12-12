<template>

    <div class="settings--group">
        <div class="settings--name">
            {{ notification_setting.name }} <small>{{ notification_setting.description }}</small>
        </div>

        <div class="settings--options">
            <template v-for="service in notification_setting.services">
            <div class="flyform--group-checkbox">
                <label>
                    <input :name="'notification_setting['+ notification_setting.id +']['+ service +']'" type="hidden" value="0">
                    <input :name="'notification_setting['+ notification_setting.id +']['+ service +']'" type="checkbox" :checked="hasNotificationSetting(notification_setting, service)" value="1">
                    <span class="icon"></span>
                    {{ service }}
                </label>
            </div>
            </template>
        </div>




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
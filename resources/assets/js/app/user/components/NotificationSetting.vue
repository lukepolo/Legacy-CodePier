<template>

    <div class="settings--group">
        <div class="settings--name">
            {{ notification_setting.name }} <small>{{ notification_setting.description }}</small>
        </div>
        <div class="settings--options">
            <template v-for="service in connectedServices">
            <div class="flyform--group-checkbox">
                <label>
                    <input :name="'notification_setting['+ notification_setting.id +']['+ service +']'" type="hidden" value="0">
                    <input
                        :name="'notification_setting['+ notification_setting.id +']['+ service +']'"
                        type="checkbox"
                        :checked="hasNotificationSetting(notification_setting, service)"
                        value="1"
                    >
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
  props: ["notification_setting"],
  methods: {
    hasNotificationSetting(notification_setting, service) {
      let notification = _.find(this.userNotificationSettings, {
        notification_setting_id: notification_setting.id
      });

      if (notification) {
        return _.indexOf(notification.services, service) !== -1;
      }
      return false;
    },
    isConnected(service) {
      if (service === "mail") {
        return true;
      }

      const provider = this.notificationProviders.find(provider => provider.provider_name === service)
        if(provider) {
            return this.userNotificationProviders.find(userProvider => {
                return userProvider.notification_provider_id === provider.id
            })
        }
        return false;
    }
  },
  computed: {
    userNotificationSettings() {
      return this.$store.state.user_notification_settings.settings;
    },
    notificationProviders() {
      return this.$store.state.notification_providers.providers;
    },
    userNotificationProviders() {
      return this.$store.state.user_notification_providers.providers;
    },
    connectedServices() {
      return this.notification_setting.services.filter(service => this.isConnected(service))
    }
  }
};
</script>

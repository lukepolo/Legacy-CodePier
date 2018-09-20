<template>

    <div class="settings--group">
        <div class="settings--name">
            {{ setting.name }} <small>{{ setting.description }}</small>
        </div>

        <div class="settings--options">
            <template v-for="service in setting.services">
            <div class="flyform--group-checkbox" v-show="isConnected(service)">
                <label>
                    <input :name="'setting['+ setting.id +']['+ service +']'" type="hidden" value="0">
                    <input
                        :name="'setting['+ setting.id +']['+ service +']'"
                        type="checkbox"
                        :checked="hasNotificationSetting(setting, service)"
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
  props: ["setting"],
  methods: {
    hasNotificationSetting(setting, service) {
      return true;
      // let notification = _.find(this.userNotificationSettings, {
      //   setting_id: setting.id,
      // });
      //
      // if (notification) {
      //   return _.indexOf(notification.services, service) !== -1;
      // }
      // return false;
    },
    isConnected(service) {
      return true;
      // if (service === "mail") {
      //   return true;
      // }
      // let provider = _.find(this.notificationProviders, "name", service);
      //
      // if (provider) {
      //   return _.find(this.userNotificationProviders, {
      //     notification_provider_id: provider.id,
      //   });
      // }
      //
      // return false;
    },
  },
  computed: {
    userNotificationSettings() {
      return this.$store.state.user_settings.settings;
    },
    notificationProviders() {
      return this.$store.state.notification_providers.providers;
    },
    userNotificationProviders() {
      return this.$store.state.user_notification_providers.providers;
    },
  },
};
</script>

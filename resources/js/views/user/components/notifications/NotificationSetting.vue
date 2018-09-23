<template>
    <div class="settings--group" v-if="userNotificationSettings.length">
        <div class="settings--name">
            {{ setting.name }} <small>{{ setting.description }}</small>
        </div>
        <div class="settings--options">
            <template v-for="service in setting.services">
                <div v-show="isConnected(service)">
                    <div
                        class="toggleSwitch--button toggleSwitch--button-switch"
                        :class="{ right : hasNotificationSetting(service) }"
                        @click="toggleSetting(service)"
                    ></div>
                    {{ service }}
                </div>
            </template>
        </div>
    </div>
</template>

<script>
export default {
  props: ["setting"],
  methods: {
    hasNotificationSetting(service) {
      return this.notificationSetting.services.includes(service);
    },
    isConnected(service) {
      if (service === "mail") {
        return true;
      }
      return this.userNotificationProviders.find((provider) => {
        let notificationProvider = this.notificationProviders.find(
          (provider) => {
            return provider.provider_name === service;
          },
        );
        return provider.notification_provider_id === notificationProvider.id;
      });
    },
    toggleSetting(service) {
      if (this.notificationSetting.services.includes(service)) {
        this.notificationSetting.services.splice(
          this.notificationSetting.services.indexOf(service),
          1,
        );
      } else {
        this.notificationSetting.services.push(service);
      }
      this.$store.dispatch(
        "user/notification/settings/update",
        this.notificationSetting,
      );
    },
  },
  computed: {
    userNotificationSettings() {
      return this.$store.state.user.notification.settings.settings;
    },
    notificationProviders() {
      return this.$store.state.notification.providers.providers;
    },
    userNotificationProviders() {
      return this.$store.state.user.notification.provider.providers;
    },
    notificationSetting() {
      return this.userNotificationSettings.find((setting) => {
        return setting.notification_setting_id === this.setting.id;
      });
    },
  },
};
</script>

<template>
    <section>
        <div class="providers grid-4">
            <label v-for="provider in notificationProviders">
                <div class="providers--item" @click="isConnected(provider.id) ? disconnectProvider(provider.id) : connectProvider(provider)">
                    <div class="providers--item-header">
                        <div class="providers--item-icon"><span :class="'icon-' + provider.name.toLowerCase()"></span></div>
                        <div class="providers--item-name">{{provider.name}}</div>
                    </div>
                    <div class="providers--item-footer">
                        <template v-if="isConnected(provider.id)">
                            <div class="providers--item-footer-disconnect"><h4><span class="icon-check_circle"></span> Disconnect</h4></div>
                        </template>
                        <template v-else>
                            <div class="providers--item-footer-connect">
                                <h4><span class="icon-link"></span> Connect Account</h4>
                            </div>
                        </template>
                    </div>
                </div>
            </label>
        </div>

        <div v-if="notificationSettings">
            <notification-group title="Site Deployment Notifications" :settings="notificationSettings['site_deployment']"></notification-group>
            <notification-group title="LifeLines Notifications" :settings="notificationSettings['lifelines']"></notification-group>
            <notification-group title="Server Monitoring Notifications" :settings="notificationSettings['server_monitoring']"></notification-group>
            <notification-group title="Buoys Notifications" :settings="notificationSettings['buoys']"></notification-group>
        </div>
    </section>

</template>

<script>
import NotificationGroup from "./components/notifications/NotificationGroup";
export default {
  components: {
    NotificationGroup,
  },
  created() {
    this.$store.dispatch("notification/settings/get");
    // this.$store.dispatch("notificationProviders/get");
    // this.$store.dispatch("user_notification_settings/get");
    // this.$store.dispatch("user_notificationProviders/get");
  },
  computed: {
    notificationSettings() {
      let settings = this.$store.state.notification.settings.settings;
      return settings.reduce((groups, setting) => {
        (groups[setting["group"]] = groups[setting["group"]] || []).push(
          setting,
        );
        return groups;
      }, {});
    },
    notificationProviders() {
      return [];
    },
    userNotificationProviders() {
      return [];
    },
  },
};
</script>

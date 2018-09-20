<template>
    <section>
        <div class="providers grid-4">
            <notification-provider-form :provider="notificationProvider" :key="notificationProvider.id" v-for="notificationProvider in notificationProviders"></notification-provider-form>
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
import NotificationProviderForm from "./components/notifications/NotificationProviderForm";

export default {
  components: {
    NotificationProviderForm,
    NotificationGroup,
  },
  created() {
    this.$store.dispatch("notification/settings/get");
    this.$store.dispatch("notification/providers/get");
    this.$store.dispatch("user/notification/settings/get");
    this.$store.dispatch("user/notification/provider/get");
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
      return this.$store.state.notification.providers.providers;
    },
  },
};
</script>

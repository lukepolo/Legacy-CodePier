<template>
  <confirm
    confirm_class="btn-link"
    confirm_position="bottom"
    message="Slack Notification Channels"
    confirm_btn="btn-primary"
  >
    <tooltip message="Notifications" placement="bottom">
      <span class="icon-notifications"></span>
    </tooltip>
    <div slot="form">
      <template v-if="hasNotificationProviders">
        <router-link :to="{ name: 'user.notification-settings' }"
          >Connect a slack account</router-link
        >
        to receive site notifications.
      </template>
      <template v-else>
        <p>
          Enter the slack channel name you want CodePier to send notifications.
        </p>
        <base-input
          name="site"
          label="Deployments Channel"
          v-model="notificationChannelsForm.site"
        ></base-input>
        <base-input
          name="servers"
          label="Servers Channel"
          v-model="notificationChannelsForm.servers"
        ></base-input>
        <base-input
          name="lifelines"
          label="Lifeline Channel"
          v-model="notificationChannelsForm.lifelines"
        ></base-input>
      </template>
    </div>
    <button slot="confirm-button" class="btn btn-small btn-primary">
      Update Channels
    </button>
  </confirm>
</template>

<script>
export default {
  props: {
    site: {
      required: true,
    },
  },
  data() {
    return {
      notificationChannelsForm: this.createForm({
        site: null,
        server: null,
        lifelines: null,
      }),
    };
  },
  watch: {
    site: {
      immediate: true,
      handler(site) {
        this.notificationChannelsForm.fill({
          site: site.id,
        });
      },
    },
  },
  methods: {
    updateNotificationChannels() {
      // this.$store
      //   .dispatch("user_sites/updateNotificationChannels", {
      //     site: this.$route.params.site_id,
      //     slack_channel_preferences: this.notificationChannelsForm.data(),
      //   })
      //   .then(() => {
      //     this.showSlackForm = false;
      //   });
    },
  },
  computed: {
    hasNotificationProviders() {
      return this.$store.state.user.notification.provider.providers.length;
    },
  },
};
</script>

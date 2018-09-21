<template>
    <label>
        <div class="providers--item" @click="selectProvider">
            <div class="providers--item-header">
                <div class="providers--item-icon"><span :class="'icon-' + provider.name.toLowerCase().replace(/\s+/g, '-')"></span></div>
                <div class="providers--item-name">{{ provider.name }}</div>
            </div>

            <div class="providers--item-footer">
                <div class="providers--item-footer-connect">
                    <form @submit.prevent="connectProvider" v-if="adding">
                        <div class="flyform--group" v-if="requiresWebhook">
                            <input type="text" v-model="form.webhook" placeholder=" ">
                            <label>Webhook URL</label>
                        </div>

                        <div class="providers--item-footer">
                            <div class="flyform--footer-btns">
                                <span class="btn" @click.stop.prevent="cancel">Cancel</span>
                                <button class="btn btn-primary">Connect</button>
                            </div>
                        </div>
                    </form>
                    <h4 v-if="!adding">
                        <template v-if="isConnected">
                            DISCONNECT
                        </template>
                        <template v-else>
                            connect account
                        </template>
                    </h4>
                </div>
            </div>
        </div>
    </label>
</template>

<script>
export default {
  props: {
    provider: {
      required: true,
    },
  },
  data() {
    return {
      adding: false,
      form: this.createForm({
        webhook: null,
      }),
    };
  },
  methods: {
    cancel() {
      this.form.reset();
      this.adding = false;
    },
    selectProvider() {
      if (this.isOauth) {
        return this.$store.dispatch(
          "user/notification/provider/redirectToProvider",
          this.provider.provider_name,
        );
      }
      this.adding = true;
    },
    connectProvider() {
      this.form
        .post(`/api/my/notification-providers/${this.provider.name}`)
        .then(() => {
          this.$store.dispatch("user_notification_providers/get");
          this.cancel();
        });
    },
  },
  computed: {
    isOauth() {
      return this.provider.connection_type === "oauth";
    },
    requiresWebhook() {
      return this.provider.connection_type === "webhook";
    },
    userNotificationProviders() {
      return this.$store.state.user.notification.provider.providers;
    },
    isConnected() {
      return this.userNotificationProviders.find((provider) => {
        return this.provider.id === provider.id;
      });
    },
  },
};
</script>

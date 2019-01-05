<template>
  <label>
    <div class="providers--item" @click="selectProvider">
      <div class="providers--item-header">
        <div class="providers--item-icon">
          <span
            :class="'icon-' + provider.name.toLowerCase().replace(/\s+/g, '-')"
          ></span>
        </div>
        <div class="providers--item-name">{{ provider.name }}</div>
      </div>

      <div class="providers--item-footer">
        <div class="providers--item-footer-connect">
          <form @submit.prevent="connectProvider" v-if="adding">
            <div class="flyform--group" v-if="requiresWebhook">
              <input type="text" v-model="form.token" placeholder=" " />
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
              Disconnect
            </template>
            <template v-else>
              Connect Account
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
        token: null,
      }),
    };
  },
  methods: {
    cancel() {
      this.form.reset();
      this.adding = false;
    },
    selectProvider() {
      if (this.isConnected) {
        this.disconnectProvider();
        return;
      }
      if (this.isOauth) {
        return this.$store.dispatch(
          "user/notification/provider/redirectToProvider",
          this.provider.provider_name,
        );
      }
      this.adding = true;
    },
    disconnectProvider() {
      this.$store.dispatch("user/notification/provider/destroy", {
        notification_provider: this.provider.id,
      });
    },
    connectProvider() {
      this.$store
        .dispatch("user/notification/provider/connectProvider", {
          data: this.form.data(),
          provider: this.provider.provider_name,
        })
        .then(() => {
          this.adding = false;
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

<template>
  <div class="banner--container">
    <div v-if="!online" class="banner banner-danger">
        It appears you are having some connection issues ....
    </div>
    <div v-if="!socketConnection" class="banner banner-danger">
        We are unable to connect you with CodePier's servers, you may not receive updates properly ....
    </div>
    <div v-if="current_version !== version" class="banner banner-info">
        <div>
            Hello, we've got a new version of CodePier ready for you! Please <a href="" class="btn btn-small">refresh now</a>
        </div>
    </div>
    <div class="banner banner-info" v-if="!user.confirmed">
        You have not confirmed your email. You will be unable to create servers until confirmed. <a class="btn btn-small" @click="resendConfirmation()">Resend Now</a>
    </div>
  </div>
</template>

<script>
import Vue from "vue";
export default Vue.extend({
  $inject: ["ConfigService", "BroadcastService"],
  data() {
    return {
      online: true,
      socketConnection: true,
      current_version: this.configService.get("env.VERSION"),
    };
  },
  created() {
    window.addEventListener("offline", () => {
      this.$set(this, "online", false);
    });

    window.addEventListener("online", () => {
      this.$set(this, "online", true);
    });

    this.interval = setInterval(() => {
      this.checkSocketConnection();
    }, 1000);
  },
  methods: {
    resendConfirmation() {
      this.$store.dispatch("user/resendConfirmationEmail");
    },
    checkSocketConnection() {
      this.$set(this, "socketConnection", this.broadcastService.isConnected());
    },
  },
  computed: {
    version() {
      return this.$store.state.system.version;
    },
  },
});
</script>

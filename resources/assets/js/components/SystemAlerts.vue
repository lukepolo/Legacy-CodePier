<template>
  <div>
      <div v-if="current_version !== version" class="text-center">
          <div>
              Hello, we've got a new version of CodePier ready for you! Please <a href="">refresh now</a>.
          </div>
      </div>
      <div class="banner banner-info" v-if="!user.confirmed">
          You have not confirmed your email. You will be unable to create servers until confirmed. <a class="btn btn-small" @click="resendConfirmation()">Resend Now</a>
      </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      current_version: Laravel.version,
    };
  },
  methods: {
    resendConfirmation() {
      this.$store.dispatch("user/resendConfirmation");
    },
  },
  computed: {
    user() {
      return this.$store.state.user.user;
    },
    version() {
      return this.$store.state.system.version;
    },
  },
};
</script>

<template>
  <div>SHOW LOADING SCREEN</div>
</template>
<script>
import Vue from "vue";
export default Vue.extend({
  mounted() {
    this.$store
      .dispatch("auth/oAuthLogin", {
        code: this.$route.query.code,
        state: this.$route.query.state,
        provider: this.$route.params.provider,
      })
      .then(() => {
        this.$router.push({ name: "dashboard" });
      });
  },
});
</script>

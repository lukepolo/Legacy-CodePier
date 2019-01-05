<template>
  <section id="middle" class="fullScreen">
    <div class="login">
      <router-link to="/" class="login--img">
        <img src="../../../img/CP_Logo-onGray.svg" alt="CodePier" />
      </router-link>

      <router-view></router-view>

      <h5 class="text-center">- Or sign in using -</h5>

      <div class="social">
        <div class="social--item">
          <a class="btn btn-primary btn-circle" @click="oauthLogin('github')"
            ><span class="icon-github"></span
          ></a>
        </div>
        <div class="social--item">
          <a class="btn btn-primary btn-circle" @click="oauthLogin('bitbucket')"
            ><span class="icon-bitbucket"></span
          ></a>
        </div>
        <div class="social--item">
          <a class="btn btn-primary btn-circle" @click="oauthLogin('gitlab')"
            ><span class="icon-gitlab"></span
          ></a>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
import Vue from "vue";

export default Vue.extend({
  beforeCreate() {
    if (this.cookieService.get("token")) {
      this.$router.push({ name: "dashboard" });
    }
  },
  $inject: ["CookieService"],
  methods: {
    oauthLogin(provider) {
      this.$store.dispatch("auth/redirectToProvider", provider);
    },
  },
  computed: {
    authAreaData() {
      return this.$store.state.auth.authAreaData;
    },
  },
});
</script>

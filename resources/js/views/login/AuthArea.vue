<template>
  <section>
    <section class="view">
      <section id="middle" class="section-column full-form">
        <div class="section-content">
          <div class="login-wrap">
            <div class="img-wrap">
              <router-link to="/">
                <img src="../../../img/CP_Logo-onGray.svg" alt="CodePier">
              </router-link>
            </div>

            <router-view></router-view>

            <h5 class="text-center"> - Or sign in using -</h5>

            <ul class="list-inline text-center">
              <li>
                <a class="btn btn-primary btn-circle" @click="oauthLogin('github')"><i class="fa fa-github"></i></a>
              </li>
              <li>
                <a class="btn btn-primary btn-circle" @click="oauthLogin('bitbucket')"><i class="fa fa-bitbucket"></i></a>
              </li>
              <li>
                <a class="btn btn-primary btn-circle" @click="oauthLogin('gitlab')"><i class="fa fa-gitlab"></i></a>
              </li>
            </ul>
          </div>
        </div>
      </section>
    </section>
  </section>
</template>

<script>
import Vue from "vue";

export default Vue.extend({
  beforeCreate() {
    if (this.CookieStorage.get("token")) {
      this.$router.push({ name: "dashboard" });
    }
  },
  $inject: ["CookieStorage"],
  methods: {
    oauthLogin(provider) {
      this.$store.dispatch("auth/oauth/redirectToProvider", provider);
    },
  },
  computed: {
    authAreaData() {
      return this.$store.state.auth.authAreaData;
    },
  },
});
</script>

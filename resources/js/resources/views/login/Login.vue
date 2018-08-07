<template>
  <section>
    <section class="view">
      <section id="middle" class="section-column full-form">
        <div class="section-content">
          <div class="login-wrap">
            <div class="img-wrap">
              <router-link to="/">
                <img src="../../../../img/CP_Logo-onGray.svg" alt="CodePier" style="display: block;">
              </router-link>
            </div>

            <login-form :formType.sync="formType" v-if="formType === 'login'"></login-form>
            <reset-password-form :formType.sync="formType" v-if="formType === 'reset'"></reset-password-form>
            <create-account-form :formType.sync="formType" v-if="formType === 'create'"></create-account-form>

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
import LoginForm from "./components/LoginForm";
import CreateAccountForm from "./components/CreateAccountForm";
import ResetPasswordForm from "./components/ResetPasswordForm";

export default Vue.extend({
  components: {
    LoginForm,
    CreateAccountForm,
    ResetPasswordForm,
  },
  data() {
    return {
      formType: "login",
    };
  },
  methods: {
    oauthLogin(provider) {
      this.$store.dispatch("auth/oauth/redirectToProvider", provider);
    },
  },
});
</script>

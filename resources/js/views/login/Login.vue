<template>
    <div id="login_form" class="login--form">
        <div class="flyform--heading">
            <h2>Login</h2>
        </div>
        <base-form v-form="form" :action="login">
            <base-input validate name="email" label="Email" type="email" v-model="form.email"></base-input>
            <base-input validate name="password" label="Password" type="password" v-model="form.password"></base-input>
            <div slot="buttons">
                <router-link @click.prevent :to="{ name : 'register' }" class="btn">Create Account</router-link>
                <button class="btn btn-primary" :class="{ 'btn-disabled' : !form.isValid()}" :disabed="!form.isValid()">Login</button>
            </div>
            <div slot="links">
                <router-link :to="{ name : 'forgot-password' }" >Forgot password?</router-link>
            </div>
        </base-form>
    </div>
</template>

<script>
import Vue from "vue";
import ShareAccountInfoMixin from "./mixins/ShareAccountInfoMixin";

export default Vue.extend({
  mixins: [ShareAccountInfoMixin],
  data() {
    return {
      form: this.createForm({
        email: this.$parent.authAreaData.email,
        password: this.$parent.authAreaData.password,
      }).validation({
        rules: {
          email: "required|email",
          password: "required|min:8",
        },
      }),
    };
  },
  methods: {
    login() {
      this.$store.dispatch("auth/login", this.form).then(() => {
        this.$router.push({
          name: "dashboard",
        });
      });
    },
  },
});
</script>

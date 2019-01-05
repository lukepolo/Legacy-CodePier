<template>
  <div class="login--form">
    <div class="flyform--heading">
      <h2>Create Account</h2>
      <p>Fill out the following fields to create your account.</p>
    </div>

    <base-form v-form="form" :action="register">
      <base-input
        validate
        name="name"
        label="Name"
        v-model="form.name"
      ></base-input>
      <base-input
        validate
        name="email"
        label="Email"
        type="email"
        v-model="form.email"
      ></base-input>
      <base-input
        validate
        name="password"
        label="Password"
        type="password"
        v-model="form.password"
      ></base-input>
      <base-input
        validate
        name="confirm-password"
        label="Confirm Password"
        type="password"
        v-model="form.passwordConfirmed"
      ></base-input>
      <div slot="buttons">
        <router-link :to="{ name: 'login' }" class="btn">Cancel</router-link>
        <button
          class="btn btn-primary"
          :class="{ 'btn-disabled': !form.isValid() }"
          :disabed="!form.isValid()"
        >
          Sign Up
        </button>
      </div>
      <div slot="links">
        <router-link :to="{ name: 'forgot-password' }"
          >Forgot password?</router-link
        >
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
        name: null,
        email: this.$parent.authAreaData.email,
        password: this.$parent.authAreaData.password,
        passwordConfirmed: null,
      }).validation({
        rules: {
          name: "required",
          email: "required|email",
          password: "required|min:8|confirmed",
        },
      }),
    };
  },
  methods: {
    register() {
      this.$store.dispatch("auth/register", this.form).then(() => {
        this.$router.push({
          name: "dashboard",
        });
      });
    },
  },
});
</script>

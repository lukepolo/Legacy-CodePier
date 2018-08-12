<template>
    <div id="forgot_form">
        <div class="flyform--heading">
            <h2>Forgot Password</h2>
        </div>

        <base-form v-form="form" :action="requestResetPassword">
            <base-input type="email" label="Email" name="email" v-model="form.email" validate></base-input>
            <div slot="buttons">
                <router-link :to="{ name : 'login' }" class="btn">Cancel</router-link>
                <button class="btn btn-primary" type="submit" :class="{ 'btn-disabled' : !form.isValid() }" :disabled="!form.isValid()">Reset Password</button>
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
        email: null,
      }).validation({
        rules: {
          email: "required|email",
        },
      }),
    };
  },
  methods: {
    requestResetPassword() {
      this.$store
        .dispatch("auth/forgotPasswordRequest", this.form)
        .then(() => {});
      this.$router.push({
        name: "login",
      });
    },
  },
});
</script>

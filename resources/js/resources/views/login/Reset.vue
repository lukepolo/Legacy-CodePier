<template>
    <div id="register_form">
        <div class="flyform--heading">
            <h2>Reset Password</h2>
        </div>

        <base-form v-form="form" :action="resetPassword">
            <base-input validate name="email" label="Email" type="email" v-model="form.email"></base-input>
            <base-input validate name="password" label="Password" type="password" v-model="form.password"></base-input>
            <base-input validate name="confirm-password" label="Confirm Password" type="password" v-model="form.passwordConfirmed"></base-input>
            <div slot="buttons">
                <button @click.prevent="$emit('update:formType', 'login')" class="btn">Cancel</button>
                <button class="btn btn-primary" :class="{ 'btn-disabled' : !form.isValid()}" :disabed="!form.isValid()">Sign Up</button>
            </div>
        </base-form>
    </div>
</template>


<script>
import Vue from "vue";

export default Vue.extend({
  props: {
    formType: {
      type: String,
      required: true,
    },
  },
  data() {
    return {
      form: this.createForm({
        email: null,
        password: null,
        passwordConfirmed: null,
      }).validation({
        rules: {
          email: "required|email",
          password: "required|min:8|confirmed",
        },
      }),
    };
  },
  methods: {
    resetPassword() {
      alert("reset password");
    },
  },
});
</script>

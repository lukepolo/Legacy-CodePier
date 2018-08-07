<template>
    <div id="login_form">
        <div class="flyform--heading">
            <h2>Login</h2>
        </div>

        <!--@include('auth.errors')-->

        <base-form v-form="form">
            <base-input validate name="email" label="Email" type="email" v-model="form.email"></base-input>
            <base-input validate name="password" label="Password" type="password" v-model="form.password"></base-input>
            <div slot="buttons">
                <button @click.prevent="$emit('update:formType', 'create')" class="btn">Create Account</button>
                <button class="btn btn-primary" :class="{ 'btn-disabled' : !form.isValid()}" :disabed="!form.isValid()">Login</button>
            </div>
            <div slot="links">
                <a @click.prevent="$emit('update:formType', 'reset')">Forgot password?</a>
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
      }).validation({
        rules: {
          email: "required|email",
          password: "required",
        },
      }),
    };
  },
});
</script>

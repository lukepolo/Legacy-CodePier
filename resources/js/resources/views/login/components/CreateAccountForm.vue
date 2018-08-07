<template>
    <div id="register_form">
        <div class="flyform--heading">
            <h2>Create Account</h2>
            <p>Fill out the following fields to create your account.</p>
        </div>
        <!--action="{{ url('/register') }}"-->
        <form method="POST">

            <div class="flyform--content">
                <div class="flyform--group">
                    <input type="text" name="name" required>
                    <label for="name">Name</label>
                </div>
                <div class="flyform--group">
                    <input type="email" name="email" required>
                    <label for="email">Email</label>
                </div>
                <div class="flyform--group">
                    <input type="password" name="password" required>
                    <label for="password">Password</label>
                </div>
                <div class="flyform--group">
                    <input type="password" name="password_confirmation" required>
                    <label for="password_confirmation">Confirm Password</label>
                </div>
            </div>

            <div class="flyform--footer">
                <div class="flyform--footer-btns">
                    <button @click.prevent="$emit('update:formType', 'login')" class="btn">Cancel</button>
                    <button class="btn btn-primary" type="submit">Sign Up</button>
                </div>
            </div>
        </form>
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

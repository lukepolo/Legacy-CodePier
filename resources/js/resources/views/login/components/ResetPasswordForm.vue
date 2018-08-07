<template>
    <div id="forgot_form">
        <div class="flyform--heading">
            <h2>Forgot Password</h2>
        </div>

        <!--action="{{ url('/password/email') }}"-->
        <form method="POST" class="validation-form floating-labels">

            <div class="flyform--content">
                <div class="flyform--group">
                    <input type="email" name="email" placeholder=" " required>
                    <label for="email">Email</label>
                </div>
            </div>
            <div class="flyform--footer">
                <div class="flyform--footer-btns">
                    <button @click.prevent="$emit('update:formType', 'login')" class="btn">Cancel</button>
                    <button class="btn btn-primary" type="submit">Reset Password</button>
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

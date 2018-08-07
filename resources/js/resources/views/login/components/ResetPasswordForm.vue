<template>
    <div id="forgot_form">
        <div class="flyform--heading">
            <h2>Forgot Password</h2>
        </div>

        <base-form v-form="form">
            <base-input type="email" label="Email" name="email" v-model="form.email" validate></base-input>
            <div slot="buttons">
                <button @click.prevent="$emit('update:formType', 'login')" class="btn">Cancel</button>
                <button class="btn btn-primary" type="submit" :class="{ 'btn-disabled' : !form.isValid() }" :disabled="!form.isValid()">Reset Password</button>
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
      }).validation({
        rules: {
          email: "required|email",
        },
      }),
    };
  },
});
</script>

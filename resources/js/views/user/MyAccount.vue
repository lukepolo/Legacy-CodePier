<template>
  <base-form v-form="form" :action="updateUser">
    <base-input label="Name" name="name" v-model="form.name">
      <span slot="append">APPEND HERE</span>
    </base-input>
    <base-input
      type="email"
      label="Email"
      name="email"
      v-model="form.email"
    ></base-input>

    <template v-if="user.password">
      <base-input
        type="password"
        label="New Password"
        name="new_password"
        v-model="form.new_password"
      ></base-input>
      <base-input
        type="password"
        label="Confirm Password"
        name="confirm_password"
        v-model="form.confirm_password"
      ></base-input>
    </template>

    <div class="flyform--group">
      <label>Workflows</label>
      <small
        >these help you build your site via tutorial style (recommended)</small
      >
    </div>

    <base-checkbox
      name="workflow"
      label="Enable"
      v-model="form.workflow"
    ></base-checkbox>

    <div class="flyform--footer">
      <div class="flyform--footer-btns">
        <button
          class="btn btn-primary"
          type="submit"
          :disabled="!form.isValid() || !form.isDirty()"
        >
          Update Profile
        </button>
      </div>
      <div class="flyform--footer-links">
        <template v-if="user.second_auth_active">
          <a @click="deactivateSecondAuth" class="text-danger"
            >Deactivate Second Authentication</a
          >
        </template>
        <template v-else>
          <template v-if="secondAuthImage">
            <br />

            <div class="grid-10">
              <div class="span-1"></div>

              <div class="span-3">
                <br />
                <img :src="secondAuthImage" />
              </div>

              <form @submit.prevent="validateSecondAuth" class="span-5">
                <div class="flex flex--baseline">
                  <div class="flyform--group flex--grow">
                    <input
                      type="text"
                      :value="secondAuthSecret"
                      readonly
                      placeholder=" "
                    />
                    <label>Secret</label>
                  </div>
                  <div class="flex--spacing">
                    <tooltip message="Copy to Clipboard" placement="top">
                      <clipboard :data="secondAuthSecret"></clipboard>
                    </tooltip>
                  </div>
                </div>

                <div class="flex flex--baseline">
                  <div class="flyform--group flex--grow">
                    <input type="text" v-model="token" placeholder=" " />
                    <label>Token</label>
                  </div>

                  <div class="flex--spacing">
                    <button class="btn btn-small btn-primary" type="submit">
                      Validate
                    </button>
                  </div>
                </div>

                <span>
                  You can use any Google compliant Two-factor authentication
                  (2FA) including
                  <a target="_blank" href="https://authy.com/download/"
                    >Authy</a
                  >
                </span>
              </form>
            </div>
          </template>
          <template v-else>
            <a @click="activateSecondAuth" class="text-success"
              >Set Up Two-factor Authentication (2FA)</a
            >
          </template>
        </template>
      </div>
    </div>
  </base-form>
</template>

<script>
import Vue from "vue";
export default Vue.extend({
  data() {
    return {
      form: this.createForm({
        user: null,
        name: null,
        email: null,
        new_password: null,
        confirm_password: null,
        workflow: null,
        second_auth_active: null,
      }),
      token: null,
      secondAuthImage: null,
      secondAuthSecret: null,
    };
  },
  watch: {
    user: "setData",
  },
  created() {
    this.setData();
  },
  methods: {
    setData() {
      this.form.merge(this.user).setAsOriginalData();
    },
    updateUser() {
      this.$store.dispatch("user/update", this.form.data());
    },
    validateSecondAuth() {
      this.$store.dispatch("auth/twoFactor/validate", this.token).then(() => {
        this.secondAuthImage = null;
        this.secondAuthSecret = null;
      });
    },
    activateSecondAuth() {
      this.$store
        .dispatch("auth/twoFactor/generateQr")
        .then(({ image, secret }) => {
          this.secondAuthImage = image;
          this.secondAuthSecret = secret;
        });
    },
    deactivateSecondAuth() {
      this.form.fill({
        second_auth_active: false,
      });
      this.updateUser();
    },
  },
});
</script>

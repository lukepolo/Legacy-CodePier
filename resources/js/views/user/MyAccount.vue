<template>
    <form @submit.prevent="updateUser">

        <base-input label="Name" name="name" v-model="form.name"></base-input>
        <base-input type="email" label="Email" name="email" v-model="form.email"></base-input>

        <template v-if="user.password">
            <base-input type="password" label="New Password" name="new_password" v-model="form.new_password"></base-input>
            <base-input type="password" label="Confirm Password" name="confirm_password" v-model="form.confirm_password"></base-input>
        </template>

        <div class="flyform--group">
            <label>Workflows</label>
            <small>these help you build your site via tutorial style (recommended)</small>
        </div>

        <base-checkbox name="workflow" label="Enable" v-model="form.workflow"></base-checkbox>

        <div class="flyform--footer">
            <div class="flyform--footer-btns">
                <button class="btn btn-primary" type="submit">Update Profile</button>
            </div>
            <div class="flyform--footer-links">
                <template v-if="user.second_auth_active">
                    <a @click="deactivateSecondAuth" class="text-error">Deactivate Second Authentication</a>
                </template>
                <template v-else>
                    <template v-if="secondAuthImage">
                        <br>

                        <div class="grid-10">
                            <div class="span-1">

                            </div>

                            <div class="span-3">
                                <br>
                                <img :src="secondAuthImage">
                            </div>


                            <form @submit.prevent="validateSecondAuth" class="span-5">
                                <div class="flex flex--baseline">
                                    <div class="flyform--group flex--grow">
                                        <input type="text" :value="secondAuthSecret" readonly placeholder=" ">
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
                                        <input type="text" v-model="token" placeholder=" ">
                                        <label>Token</label>
                                    </div>

                                    <div class="flex--spacing">
                                        <button class="btn btn-small btn-primary" type="submit">Validate</button>
                                    </div>
                                </div>

                                <span>
                                    You can use any Google compliant Two-factor authentication (2FA) including <a target="_blank" href="https://authy.com/download/">Authy</a>
                                </span>
                            </form>
                        </div>
                    </template>
                    <template v-else>
                        <a @click="activateSecondAuth" class="text-success">Set Up Two-factor Authentication (2FA)</a>
                    </template>
                </template>
            </div>
        </div>
    </form>
</template>

<script>
export default {
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
      this.form.name = this.user.name;
      this.form.email = this.user.email;
      this.form.workflow = this.user.workflow;
      this.form.second_auth_active = this.user.second_auth_active;
    },
    updateUser() {
      this.form.user_id = this.user.id;
      this.$store.dispatch("user/update", this.form);
    },
    validateSecondAuth() {
      this.$store.dispatch("auth/validateSecondAuth", this.token);
    },
    activateSecondAuth() {
      this.$store.dispatch("auth/getSecondAuthQr").then((secondAuth) => {
        this.secondAuthImage = secondAuth.image;
        this.secondAuthSecret = secondAuth.secret;
      });
    },
    deactivateSecondAuth() {
      this.form.second_auth_active = false;
      this.updateUser();
    },
  },
};
</script>

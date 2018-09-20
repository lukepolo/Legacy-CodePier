<template>
    <div>
        <label>
            <div class="providers--item" @click="adding = true">
                <div class="providers--item-header">
                    <div class="providers--item-icon"><span :class="'icon-' + provider.name.toLowerCase().replace(/\s+/g, '-')"></span></div>
                    <div class="providers--item-name">{{provider.name}}</div>
                </div>

                <div class="providers--item-footer">
                    <div class="providers--item-footer-connect">
                        <form @submit.prevent="connectProvider" v-if="adding">
                            <div class="flyform--group">
                                <input type="text" v-model="form.account" placeholder=" ">
                                <label>Account Name</label>
                            </div>

                            <div class="flyform--group">
                                <input type="text" v-model="form.token" placeholder=" ">
                                <label>Api Token</label>
                            </div>

                            <div class="flyform--group" v-if="provider.secret_token">
                                <input type="text" v-model="form.secret_token" placeholder=" ">
                                <label>Secret Token</label>
                            </div>

                            <br>

                            <div class="providers--item-footer">
                                <div class="flyform--footer-btns">
                                    <span class="btn" @click.stop.prevent="cancel">Cancel</span>
                                    <button class="btn btn-primary">Connect</button>
                                </div>
                            </div>
                        </form>
                        <h4 v-if="!adding">
                            connect account
                        </h4>
                    </div>
                </div>
            </div>
            <br>
            <div class="text-center" v-if="provider.name === 'Digital Ocean'">
                <a target="_blank" href="https://m.do.co/c/27ffab8712be">Get Free $10 for<strong>&nbsp;New Accounts</strong></a>
            </div>
        </label>
    </div>
</template>

<script>
export default {
  props: {
    provider: {
      required: true,
    },
  },
  data() {
    return {
      adding: false,
      form: this.createForm({
        token: null,
        account: null,
        secret_token: null,
      }),
    };
  },
  methods: {
    cancel() {
      this.adding = false;
      this.form.reset();
    },
    connectProvider() {
      this.$store
        .dispatch("user/serverProviders/connectProvider", {
          data: this.form.data(),
          provider: this.provider,
        })
        .then(() => {
          this.cancel();
        });
    },
  },
  computed: {
    userServerProviders() {
      return this.$store.state.user.serverProviders.providers;
    },
  },
};
</script>

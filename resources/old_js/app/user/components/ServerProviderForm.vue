<template>
    <section>

        <template v-if="adding">

            <form @submit.prevent="connectProvider">
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
                        <span class="btn" @click.prevent.stop="cancel">Cancel</span>
                        <button class="btn btn-primary">Connect</button>
                    </div>
                </div>

            </form>

        </template>

        <span v-if="!adding">
            connect account
        </span>

    </section>
</template>


<script>
export default {
  props: {
    provider: {
      default: null,
    },
    adding: {
      default: false,
    },
  },
  data() {
    return {
      form: this.createForm({
        token: null,
        account: null,
        secret_token: null,
      }),
    };
  },
  methods: {
    cancel() {
      Vue.set(this.form, "token", null);
      Vue.set(this.form, "account", null);
      Vue.set(this.form, "secret_token", null);

      this.$emit("update:adding", false);
    },
    connectProvider() {
      this.form
        .post(
          "/api/server/providers/" + this.provider.provider_name + "/provider ",
        )
        .then(() => {
          this.$store.dispatch(
            "user_server_providers/get",
            this.$store.state.user.user.id,
          );
          this.cancel();
        });
    },
  },
};
</script>

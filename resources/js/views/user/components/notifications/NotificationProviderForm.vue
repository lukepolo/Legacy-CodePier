<template>
    <section>

        <template v-if="adding">

            <form @submit.prevent="connectProvider">

                <div class="flyform--group" v-if="provider.connection_type === 'webhook'">
                    <input type="text" v-model="form.token" placeholder=" ">
                    <label>Webhook</label>
                </div>

                <div class="providers--item-footer">
                    <div class="flyform--footer-btns">
                        <span class="btn" @click.prevent.stop="cancel">Cancel</span>
                        <button class="btn btn-primary">Connect</button>
                    </div>
                </div>

            </form>

        </template>

        <span v-if="!adding">
          <h4>Connect Account</h4>
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
      }),
    };
  },
  methods: {
    cancel() {
      Vue.set(this.form, "token", null);

      this.$emit("update:adding", false);
    },
    connectProvider() {
      this.form
        .post(`/api/my/notification-providers/${this.provider.name}`)
        .then(() => {
          this.$store.dispatch("user_notification_providers/get");
          this.cancel();
        });
    },
  },
};
</script>

<template>
    <form @submit.prevent="saveSite" v-if="adding && form.pile_id">
        <div class="flyform--group">
            <input ref="domain" name="domain" v-model="form.domain" type="text" placeholder=" ">
            <label for="domain">Domain / Alias</label>
        </div>
        <div class="flyform--group-checkbox">
            <label>
                <input type="checkbox" v-model="form.wildcard_domain" name="wildcard_domain" value="1">
                <span class="icon"></span>
                Wildcard Domain
                <tooltip :message="'If your site requires wildcard for sub domains'" size="medium">
                    <span class="fa fa-info-circle"></span>
                </tooltip>
            </label>
        </div>
        <div class="flyform--footer">
            <div class="flyform--footer-btns">
                <span class="btn" @click.stop="cancel">Cancel</span>
                <button class="btn btn-primary">Save</button>
            </div>
        </div>

    </form>
</template>

<script>
export default {
  props: {
    pile: {
      default: null
    },
    adding: {
      default: false
    }
  },
  data() {
    return {
      form: this.createForm({
        domain: null,
        wildcard_domain: false,
        pile_id: this.pile && this.pile.id
      })
    };
  },
  watch: {
    adding: function() {
      Vue.nextTick(() => {
        if (this.adding) {
          this.$refs.domain.focus();
        }
      });
    }
  },
  methods: {
    saveSite() {
      this.$store.dispatch("user_sites/store", this.form);
    },
    cancel() {
      this.$emit("update:adding", false);
    }
  }
};
</script>

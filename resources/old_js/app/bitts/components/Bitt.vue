<template>
    <div class="group--item">
        <div class="group--item-heading">
            <h4>
                <div class="group--item--heading-name">
                    <h4>
                        {{ bitt.title }}
                    </h4>
                </div>
                <div class="action-btn" v-if="bitt.user_id === user.id">
                    <router-link :to="{ name: 'bitt_edit', params : { bitt_id : bitt.id } }" class="btn btn-small">
                        <span class="icon-pencil"></span>
                    </router-link>
                    <confirm confirm_class="btn btn-small btn-danger" :confirm_action="deleteBitt">
                        <span class="fa fa-close"></span>
                    </confirm>
                </div>
            </h4>
        </div>
        <div class="group--item-subheading">
            <div class="group--item-subheading-info">
                <template v-for="category in bitt.categories">
                    {{ category.name }}
                </template>
            </div>
            <div class="group--item-subheading-info">
                {{ bitt.uses }} Uses
            </div>
        </div>
        <div class="group--item-content">
            <div class="lineclamp">
                {{ bitt.description }}
            </div>
        </div>

        <div class="btn-footer text-center">
            <button class="btn" @click.prevent="runForm">
                View Bitt
            </button>
        </div>
    </div>
</template>

<script>
export default {
  props: ["bitt"],
  methods: {
    runForm() {
      this.$store.commit("bitts/view", this.bitt);
    },
    deleteBitt() {
      this.$store.dispatch("bitts/destroy", this.bitt.id).then(() => {
        this.$emit("searchBitts", true);
      });
    },
  },
  computed: {
    user() {
      return this.$store.state.user.user;
    },
  },
};
</script>

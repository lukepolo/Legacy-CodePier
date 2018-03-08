<template>
    <div class="group">
        <div class="group-heading">
            <h4>
                {{ bitt.title }}

                <p class="pull-right">
                    {{ bitt.uses }} uses
                    <br>
                    <router-link :to="{ name: 'bitt_edit', params : { bitt_id : bitt.id} }">
                        Edit
                    </router-link>
                </p>

                <div class="small">
                    <template v-for="category in bitt.categories">
                        {{ category.name }}
                    </template>
                </div>

            </h4>
        </div>

        <div class="group-content">
            {{ bitt.description }}
        </div>

        <div class="btn-footer text-center">
            <button class="btn btn-primary" @click.prevent="runForm">
                Run
            </button>
        </div>
    </div>
</template>

<script>
export default {
  props: ["bitt"],
  methods: {
    runForm() {
      this.$store.commit('bitts/set', this.bitt)
    },
    deleteBitt() {
      this.$store.dispatch("deleteBitt", this.bitt.id);
    }
  }
};
</script>

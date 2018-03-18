<template>
    <div class="group--item">
        <div class="group--item-heading">
            <h4>
                <div class="group--item--heading-name">
                    <h4>
                        {{ bitt.title }}
                    </h4>
                </div>
                <div class="action-btn">
                    <router-link :to="{ name: 'bitt_edit', params : { bitt_id : bitt.id} }" class="btn btn-small">
                        <span class="icon-pencil"></span>
                    </router-link>
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
    props: ['bitt'],
    methods: {
      runForm () {
        this.$store.commit('bitts/set', this.bitt)
      },
      deleteBitt () {
        this.$store.dispatch('deleteBitt', this.bitt.id)
      }
    }
  }
</script>

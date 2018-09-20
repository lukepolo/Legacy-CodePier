<template>
    <div class="parent">
        <!--<section id="left" class="column">-->
            <!--<h3 class="column--header">Search</h3>-->
        <!--</section>-->
        <section id="middle" class="column">
            <h3 class="column--header primary">
               Bitts
            </h3>
            <div class="column--content">
                <div class="container">

                    <transition>
                        <router-view name="nav"></router-view>
                    </transition>

                    <transition>
                        <router-view></router-view>
                    </transition>

                </div>
            </div>
        </section>

        <section id="right" class="column" v-if="bitt">
            <h3 class="column--header">Run Bitt</h3>
            <router-view name="right"></router-view>
        </section>

    </div>
</template>

<script>
export default {
  computed: {
    bitt() {
      return this.$store.state.bitts.viewBitt;
    },
  },
};
</script>

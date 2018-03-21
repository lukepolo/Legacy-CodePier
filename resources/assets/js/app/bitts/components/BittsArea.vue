<template>
    <div class="parent">
        <!--<section id="left" class="section-column">-->
            <!--<h3 class="section-header">Search</h3>-->
        <!--</section>-->
        <section id="middle" class="section-column">
            <h3 class="section-header primary">
               Bitts
            </h3>
            <div class="section-content">
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

        <section id="right" class="section-column" v-if="bitt">
            <h3 class="section-header">Run Bitt</h3>
            <router-view name="right"></router-view>
        </section>

    </div>
</template>

<script>
export default {
  computed: {
    bitt() {
      return this.$store.state.bitts.bitt;
    }
  }
};
</script>

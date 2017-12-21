<template>
    <div class="parent">
        <section id="left" class="section-column">
            <h3 class="section-header">Search</h3>
        </section>
        <section id="middle" class="section-column">
            <h3 class="section-header primary">Buoys</h3>
            <div class="section-content">
                <div class="container">
                    <router-view name="nav"></router-view>
                    <router-view></router-view>
                </div>
            </div>
        </section>

        <section id="right" class="section-column" v-if="buoy">
            <router-view name="right"></router-view>
        </section>
    </div>
</template>

<script>
export default {
  computed: {
    buoy() {
      return this.$store.state.buoys.buoy_app;
    }
  }
};
</script>
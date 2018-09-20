<template>
    <div class="parent">
        <section id="left" class="column">
            <h3 class="column--header">Search</h3>
        </section>
        <section id="middle" class="column">
            <h3 class="column--header primary">Buoys</h3>
            <div class="column--content">
                <div class="container">
                    <router-view name="nav"></router-view>
                    <router-view></router-view>
                </div>
            </div>
        </section>

        <section id="right" class="column" v-if="buoy">
          <h3 class="column--header">Install Buoy</h3>
          <router-view name="right"></router-view>
        </section>
    </div>
</template>

<script>
export default {
  computed: {
    buoy() {
      return this.$store.state.buoys.buoy_app;
    },
  },
};
</script>

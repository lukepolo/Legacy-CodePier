<template>
    <section>
        <div class="group">
            <buoy-app :buoyApp="buoyApp" v-for="buoyApp in buoyApps" :key="buoyApp.id"></buoy-app>
        </div>
    </section>
</template>

<script>
import { BuoyApp } from "../components";

export default {
  components: {
    BuoyApp
  },
  created() {
    this.$store.dispatch("buoys/get");
    this.$store.dispatch("user_server_buoys/all");
  },
  computed: {
    buoyApps() {
      if (this.buoyAppsPagination) {
        return this.buoyAppsPagination.data;
      }
    },
    buoyAppsPagination() {
      return this.$store.state.buoys.buoy_apps;
    }
  }
};
</script>

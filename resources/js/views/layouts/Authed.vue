<template>
  <div id="app" v-if="user">
    <system-alerts></system-alerts>
    <navigation></navigation>
    <left-nav></left-nav>
    <router-view class="view"></router-view>

    <!--<events-bar v-if="hasSites"></events-bar>-->
    <!-- <announcements></announcements> -->
    <!-- <portal-target name="modal" slim></portal-target> -->

    <portal-target slim name="right-nav"></portal-target>
  </div>
</template>

<script>
import Vue from "vue";
import LeftNav from "./components/LeftNav";
import Navigation from "./components/Navigation";
import SystemAlerts from "./components/SystemAlerts";
import Announcements from "./components/Announcements";

export default Vue.extend({
  components: {
    LeftNav,
    Navigation,
    SystemAlerts,
    Announcements,
  },
  mounted() {
    this.$store.dispatch("user/piles/get");
    this.$store.dispatch("user/sites/get");
  },
});
</script>

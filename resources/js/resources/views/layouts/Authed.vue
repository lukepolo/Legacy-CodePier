<template>
    <div id="app" v-if="user">
        <system-alerts></system-alerts>
        <navigation></navigation>
        <div id="xlarge-wrap">
            <div id="main">
                <router-view class="view"></router-view>
            </div>
            <!--<events-bar v-if="hasSites"></events-bar>-->
        </div>
        <!--<announcements></announcements>-->
        <!--<portal-target name="modal" slim></portal-target>-->
    </div>
</template>

<script>
import Vue from "vue";
import Navigation from "./components/Navigation";
import SystemAlerts from "./components/SystemAlerts";
export default Vue.extend({
  components: {
    Navigation,
    SystemAlerts,
  },
  mounted() {
    this.$store.dispatch("user/piles/get");
  },
});
</script>

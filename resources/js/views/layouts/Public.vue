<template>
  <div id="app">
    <alerts></alerts>
    <router-view></router-view>
  </div>
</template>

<script>
import Vue from "vue";
import Alerts from "./components/Alerts";

export default Vue.extend({
  components: {
    Alerts,
  },
});
</script>

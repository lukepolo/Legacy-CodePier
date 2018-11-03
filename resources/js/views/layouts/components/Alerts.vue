<template id="alerts">
    <div class="alerts">
        <transition-group name="alert-animation">
            <alert :alert="alert" :key="alert.id" v-for="alert in alerts"></alert>
        </transition-group>
    </div>
</template>

<script>
import Vue from "vue";
import Alert from "./alert-components/Alert";

export default Vue.extend({
  components: {
    Alert,
  },
  computed: {
    alerts() {
      return this.$store.state.varie.alerts.alerts;
    },
  },
});
</script>

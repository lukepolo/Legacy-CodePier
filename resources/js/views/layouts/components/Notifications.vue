<template id="notifications">
    <div class="notifications">
        <transition-group name="notification-animation">
            <notification :notification="notification" :key="notification.id" v-for="notification in notifications"></notification>
        </transition-group>
    </div>
</template>

<script>
import Vue from "vue";
import Notification from "./notification-components/Notification";

export default Vue.extend({
  components: {
    Notification,
  },
  computed: {
    notifications() {
      return this.$store.state.varie.notifications.notifications;
    },
  },
});
</script>

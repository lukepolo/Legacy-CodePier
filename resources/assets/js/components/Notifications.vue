<template id="notifications">
    <div class="notifications">
        <notification
            v-for="(notification, index) in notifications"
            :notification="notification"
            :key="index"
        >
        </notification>
    </div>
</template>

<script>
    import Notification from './Notification.vue';
    export default {
        components: {
            Notification
        },
        computed: {
            notifications() {
                return this.$store.state.notifications.notifications;
            }
        },
        methods: {
            removeNotification: function (notification) {
                this.$store.dispatch('notifications/remove',notification)
            }
        }
    }
</script>
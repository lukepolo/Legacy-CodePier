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
    import Notification from './../components/Notification.vue';
    export default {
        components: {
            Notification
        },
        computed: {
            notifications() {
                return this.$store.state.notificationsStore.notifications;
            }
        },
        methods: {
            removeNotification: function (notification) {
                this.$store.dispatch('removeNotification',notification)
            }
        }
    }
</script>
<template>
    <transition name="fade">
        <div class="notification" :class="notification.class">
            <button @click="close(notification)" class="notification-close" type="button">
                <span>&times;</span>
            </button>
            <h4 class="notification-heading" v-if="notification.title">{{notification.title}}</h4>
            <div class="notification-text" v-html="notification.text"></div>
        </div>
    </transition>
</template>
<script>
    export default {
        props: ['notification'],
        data() {
            return {
                timer: null
            }
        },
        created() {
            let timeout = this.notification.hasOwnProperty('timeout') ? this.notification.timeout : true;
            if (timeout) {
                this.timer = setTimeout(function () {
                    this.close(this.notification)
                }.bind(this),  this.notification.timeout);
            }
        },
        methods : {
            close: function (notification) {
                clearTimeout(this.timer);
                this.$store.dispatch('notifications/remove', notification);
            }
        }
    }
</script>
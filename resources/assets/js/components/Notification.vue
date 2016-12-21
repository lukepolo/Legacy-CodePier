<template>
    <transition name="fade">
        <div class="notification" :class="notification.class">
            <button @click="close(notification)" class="close-button" type="button">
                <span>&times;</span>
            </button>
            <h5 v-if="notification.title">{{notification.title}}</h5>
            <p v-html="notification.text"></p>
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
                let delay = this.notification.delay || 3000;
                this.timer = setTimeout(function () {
                    this.close(this.notification)
                }.bind(this), delay);
            }
        },
        methods : {
            close: function (notification) {
                clearTimeout(this.timer);
                this.$store.dispatch('removeNotification', notification);
            }
        }
    }
</script>
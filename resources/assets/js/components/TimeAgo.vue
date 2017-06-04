<template>
    <span> {{ text }} </span>
</template>

<script>
    export default {
        props: {
            time: {}
        },
        mounted () {
            this.setCurrentTime()
            setInterval(() => {
                this.update()
            }, 60000)
        },
        data () {
            return {
                currentTime: null
            }
        },
        watch: {
            time: function () {
                this.setCurrentTime()
            }
        },
        methods: {
            update () {
                Vue.set(this.currentTime, this.currentTime.add(-1, 'minute'))
            },
            setCurrentTime () {
                this.currentTime = this.time
            }
        },
        computed: {
            text () {
                if (this.currentTime) {
                    return this.currentTime
                        .fromNow()
                        .replace('ute', '')
                        .replace('ago', '')
                }
            }
        }
    }
</script>

<template>
    <span> {{ text }} </span>
</template>

<script>
    export default {
        props : {
            time : {}
        },
        created() {
            this.currentTime = this.time
            setInterval(() => {
                this.update()
            }, 60000)
        },
        data() {
          return {
              currentTime : null
          }
        },
        methods : {
            update() {
                Vue.set(this.currentTime, this.currentTime.add(-1, 'minute'))
            }
        },
        computed : {
            text() {
                if(this.currentTime) {
                    return this.currentTime
                        .fromNow()
                        .replace('ute', '')
                        .replace('ago', '')
                }
            }
        }
    }
</script>
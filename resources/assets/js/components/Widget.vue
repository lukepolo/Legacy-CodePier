<template>
    <div @click="show = !show">
        <span :class="icon"></span>
        <slot v-if="show"></slot>
    </div>
</template>

<script>
    export default {
        props : {
            icon : {
                default : '',
            }
        },
        data() {
            return {
                show : false,
            }
        },
        methods : {

        },
        computed: {

        }
    }
</script>
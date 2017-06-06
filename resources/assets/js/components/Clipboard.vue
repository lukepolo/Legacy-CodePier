<template>
    <a class="btn btn-small clipboard" :data-clipboard-text="data">
        <span class="icon-clipboard"></span>
    </a>
</template>

<script>
    export default {
        props: {
            data: String
        },
        created () {
            new Clipboard('.clipboard', {
                text: this.data
            })
        }
    }
</script>

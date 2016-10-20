<template>
    <section>
        here
    </section>
</template>

<script>
    export default {
        computed: {
            servers() {
                return serverStore.state.servers;
            }
        },
        created() {
            alert('test');
            serverStore.dispatch('getServers');
        }
    }
</script>
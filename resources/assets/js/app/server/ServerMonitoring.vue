<template>
    <section v-if="server">
        <cpu-loads :stats="server.stats" showLabels="true
"></cpu-loads>
    </section>
</template>

<script>
    import CpuLoads from '../site/components/CpuLoadVue.vue'
    export default {
        components : {
            CpuLoads
        },
        created() {
            this.fetchData();
        },
        watch: {
            '$route': 'fetchData'
        },
        methods: {
            fetchData() {
            }
        },
        computed: {
            server() {
                return this.$store.state.serversStore.server;
            }
        }
    }
</script>

<template>
    <section>
        <section id="middle" class="section-column" v-if="server">
        </section>
    </section>
</template>

<script>
    export default {
        data() {
            return {}
        },
        created() {
            this.fetchData();
        },
        watch: {
            '$route': 'fetchData'
        },
        methods: {
            fetchData() {
                this.$store.dispatch('getServer', this.$route.params.server_id);
            }
        },
        computed: {
            server() {
                return this.$store.state.serversStore.server;
            }
        }
    }
</script>

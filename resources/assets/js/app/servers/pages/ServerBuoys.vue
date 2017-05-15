<template>
    <div>
        <template v-for="buoy in serverBuoys">
            {{ buoy.buoy_app.title }} <a href="#" @click.prevent="removeBuoy(buoy.id)">Remove</a><br>
        </template>
    </div>
</template>

<script>
    export default {
        created() {
            this.fetchData();
        },
        watch: {
            '$route': 'fetchData'
        },
        methods: {
            fetchData() {
                this.$store.dispatch('user_server_buoys/get', this.$route.params.server_id)
            },
            removeBuoy(buoyId) {
                this.$store.dispatch('user_server_buoys/destroy', {
                    buoy : buoyId,
                    server : this.$route.params.server_id
                });
            }
        },
        computed: {
            server() {
                return this.$store.state.user_servers.server
            },
            serverBuoys() {
                return this.$store.state.user_server_buoys.buoys
            },
        },
    }
</script>
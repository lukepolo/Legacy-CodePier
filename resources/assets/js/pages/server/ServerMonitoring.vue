<template>
    <section>
        <left-nav></left-nav>
        <section id="middle" class="section-column" v-if="server">
            <server-nav :server="server"></server-nav>
        </section>
    </section>
</template>

<script>
    import LeftNav from './../../core/LeftNav.vue';
    import ServerNav from './components/ServerNav.vue';

    export default {
        components : {
            LeftNav,
            ServerNav,
        },
        data() {
            return {

            }
        },
        created() {
            this.fetchData();
        },
        watch: {
            '$route': 'fetchData'
        },
        methods: {
            fetchData: function () {
                this.$store.dispatch('getServer', this.$route.params.server_id);
            }
        },
        computed : {
            server : () => {
                return this.$store.state.serversStoreserver;
            }
        }
    }
</script>

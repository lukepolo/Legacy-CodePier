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
    import Blackfire from './components/Blackfire.vue';

    export default {
        components : {
            LeftNav,
            ServerNav,
            Blackfire
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
                serverStore.dispatch('getServer', this.$route.params.server_id);
            }
        },
        computed : {
            server : () => {
                return serverStore.state.server;
            }
        }
    }
</script>

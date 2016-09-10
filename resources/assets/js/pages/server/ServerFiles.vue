<template>
    <section>
        <left-nav></left-nav>
        <section id="middle" class="section-column" v-if="server">
            <server-nav :server="server"></server-nav>
            <editable-server-files :server="server.id"></editable-server-files>
        </section>
    </section>
</template>

<script>
    import LeftNav from './../../core/LeftNav.vue';
    import ServerNav from './components/ServerNav.vue';
    import EditableServerFiles from './components/EditableServerFiles.vue'

    export default {
        components: {
            LeftNav,
            ServerNav,
            EditableServerFiles
        },
        created() {
            this.fetchData();
        },
        watch: {
            '$route': 'fetchData'
        },
        methods: {
            fetchData: function() {
                serverStore.dispatch('getServer', this.$route.params.server_id);
            }
        },
        computed: {
            server : () => {
                return serverStore.state.server;
            }
        }
    }
</script>
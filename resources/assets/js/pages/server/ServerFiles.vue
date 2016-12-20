<template>
    <section id="middle" class="section-column" v-if="server">
        <editable-server-files :server="server.id"></editable-server-files>
    </section>
</template>

<script>
    import LeftNav from './../../core/LeftNav.vue';
    import ServerNav from './components/ServerNav.vue';
    import EditableServerFiles from './components/EditableServerFiles.vue'

    export default {
        components: {
            EditableServerFiles
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
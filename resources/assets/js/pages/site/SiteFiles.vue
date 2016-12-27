<template>
    <div>
        <framework-files v-if="site && site.framework"></framework-files>
        <server-files v-if="site"></server-files>
    </div>
</template>

<script>
    import ServerFiles from  './components/SiteServerFiles.vue';
    import FrameworkFiles from './components/FrameworkFiles.vue';
    export default {
        components : {
            ServerFiles,
            FrameworkFiles
        },
        created() {
            this.fetchData();
        },
        watch: {
            '$route': 'fetchData'
        },
        methods: {
            fetchData() {
                this.$store.dispatch('getSite', this.$route.params.site_id);
            }
        },
        computed: {
            site() {
                return this.$store.state.sitesStore.site;
            }
        }
    }
</script>
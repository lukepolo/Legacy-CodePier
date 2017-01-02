<template>
    <div v-if="site">
        <framework-files v-if="site.framework"></framework-files>
        <server-files></server-files>
        <custom-files></custom-files>
    </div>
</template>

<script>
    import CustomFiles from  './components/SiteCustomFiles.vue';
    import ServerFiles from  './components/SiteServerFiles.vue';
    import FrameworkFiles from './components/FrameworkFiles.vue';
    export default {
        components : {
            CustomFiles,
            ServerFiles,
            FrameworkFiles,
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
                this.$store.dispatch('getSiteFiles', this.$route.params.site_id);
            }
        },
        computed: {
            site() {
                return this.$store.state.sitesStore.site;
            }
        }
    }
</script>
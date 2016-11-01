<template>
    <div v-if="site">
        <site-workers :site="site"></site-workers>
        <site-cron-jobs :site="site"></site-cron-jobs>
    </div>
</template>

<script>
    import SiteWorkers from './components/SiteWorkers.vue';
    import SiteCronJobs from './components/SiteCronJobs.vue';
    export default {
        components : {
            SiteWorkers,
            SiteCronJobs
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
            },
        },
        computed: {
            site() {
                return this.$store.state.sitesStore.site;
            }
        }
    }
</script>
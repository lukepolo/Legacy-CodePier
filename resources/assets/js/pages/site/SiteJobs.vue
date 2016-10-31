<template>
    <div v-if="site">
        <site-workers :site="site"></site-workers>
        CronJobs here
        <template v-for="cronJob in cronJobs">
            {{ cronJob.id }}
        </template>
    </div>
</template>

<script>
    import SiteWorkers from './components/SiteWorkers.vue';
    export default {
        components : {
            SiteWorkers
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
            fetchData() {
                this.$store.dispatch('getSite', this.$route.params.site_id);
            }
        },
        computed: {
            site() {
                return this.$store.state.sitesStore.site;
            },
            cronJobs() {
                return this.$store.state.siteCronJobsStore.site_cron_jobs;
            }
        }
    }
</script>
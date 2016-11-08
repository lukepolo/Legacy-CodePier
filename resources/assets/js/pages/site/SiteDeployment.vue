<template>
    <div v-if="site">
        Site Deployments
        <div v-for="(deploymentOptions, type) in deploymentOptions">
            <p v-for="deploymentOption in deploymentOptions">
                {{ deploymentOption.task }} : <small>{{ deploymentOption.description }}</small>
            </p>
        </div>
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
                this.$store.dispatch('getSite', this.$route.params.site_id);
                this.$store.dispatch('getSiteDeploymentOptions', this.$route.params.site_id);
            },
        },
        computed: {
            site() {
                return this.$store.state.sitesStore.site;
            },
            deploymentOptions() {
                return this.$store.state.sitesStore.site_deployment_options;
            }
        }
    }
</script>
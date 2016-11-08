<template>
    <div v-if="site">
        Site Deployments
        <form @submit.prevent="updateSiteDeployment">
            <div v-for="deploymentOption in deploymentOptions">
                <p>
                    {{ deploymentOption.name }} <br> <small>{{ deploymentOption.description }}</small>
                    <input type="checkbox" name="" value="true">
                </p>
            </div>
        </form>
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
                this.$store.dispatch('getDeploymentSteps', this.$route.params.site_id);
                this.$store.dispatch('getSiteDeploymentSteps', this.$route.params.site_id);
            },
        },
        computed: {
            site() {
                return this.$store.state.sitesStore.site;
            },
            deploymentOptions() {
                return this.$store.state.sitesStore.deployment_steps;
            },
            currentSiteDeployment() {
                return this.$store.state.siteStore.current_deployment_steps;
            },
            updateSiteDeployment() {
                alert('got here');
//                this.$store.dispatch('updatSiteDeployment', {
//                    site : this.$route.params.site_id,
//                    deployment_steps : []
//                })
            }
        }
    }
</script>
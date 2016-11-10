<template>
    <div v-if="site">
        Site Deployments
        <form @submit.prevent="updateSiteDeployment">
            <div v-for="deploymentStep in deploymentSteps">
                <p>
                    {{ deploymentStep.name }} <br> <small>{{ deploymentStep.description }}</small>
                    <input type="checkbox" name="deploymentSteps[]" :value="deploymentStep.name" :checked="hasStep(deploymentStep.task)">
                </p>
            </div>
            <button type="submit">Update Deployment Steps</button>
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
            updateSiteDeployment() {
                this.$store.dispatch('updateSiteDeployment', {
                    site : this.$route.params.site_id,
                    deployment_steps : this.getFormData(this.$el)
                })
            },
            hasStep(task) {
                if(this.currentSiteDeploymentSteps.length) {
                    return _.find(this.currentSiteDeploymentSteps, {'internal_deployment_function' : task});
                }
                return false;
            }
        },
        computed: {
            site() {
                return this.$store.state.sitesStore.site;
            },
            deploymentSteps() {
                return this.$store.state.sitesStore.deployment_steps;
            },
            currentSiteDeploymentSteps() {
                return this.$store.state.sitesStore.site_deployment_steps;
            }
        }
    }
</script>
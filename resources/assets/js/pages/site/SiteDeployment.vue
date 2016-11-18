<template>
    <div v-if="site">
        Site Deployments
        <form @submit.prevent="updateSiteDeployment">
            <div class="drag">
                <h2>Inactive</h2>
                <draggable :list="inactive" class="dragArea" :options="{group:'tasks'}">
                    <div v-for="deploymentStep in inactive">
                        {{ deploymentStep.step }} <br> <small>{{ deploymentStep.description }}</small>
                    </div>
                </draggable>
                <h2>Active</h2>
                <draggable :list="active" class="dragArea" :options="{group:'tasks'}">
                    <div v-for="deploymentStep in active">
                        {{ deploymentStep.step }} <br> <small>{{ deploymentStep.description }}</small>
                    </div>
                </draggable>
            </div>

            <div class="btn-footer"><button type="submit" class="btn btn-primary">Update Deployment</button></div>
        </form>
    </div>
</template>

<script>
    import draggable from 'vuedraggable';
    export default {
        components: {
            draggable
        },
        data() {
            return {
                active: [],
                inactive: [],
            }
        },
        created() {
            this.fetchData();
        },
        watch: {
            '$route': 'fetchData',
        },
        methods: {
            fetchData() {
                this.$store.dispatch('getSite', this.$route.params.site_id);
                this.$store.dispatch('getDeploymentSteps', this.$route.params.site_id).then((possibleDeploymentSteps) => {
                    this.$store.dispatch('getSiteDeploymentSteps', this.$route.params.site_id).then((currentDeploymentSteps) => {

                        this.active = [];
                        this.inactive = [];

                        _.each(currentDeploymentSteps, (step) => {
                            if(step.script) {
                               alert('custom script, need to fix');
                            } else {
                                this.active.push(_.find(possibleDeploymentSteps, { internal_deployment_function : step.internal_deployment_function }));
                            }
                        });

                        _.each(possibleDeploymentSteps, (step) => {
                            if(!this.hasStep(step.internal_deployment_function)) {
                                this.inactive.push(step);
                            }
                        });

                    });
                });

            },
            updateSiteDeployment() {
                this.$store.dispatch('updateSiteDeployment', {
                    site : this.$route.params.site_id,
                    deployment_steps : this.active
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
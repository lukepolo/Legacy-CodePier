<template>
    <div v-if="site">
        <div style="display: none">
            {{ deploymentSteps }}
        </div>

        Site Deployments
        <form @submit.prevent="updateSiteDeployment">
            <div class="drag">
                <h2>Inactive</h2>
                <draggable :list="inactive" class="dragArea">
                    <div v-for="deploymentStep in inactive">
                        {{ deploymentStep.name }} <br> <small>{{ deploymentStep.description }}</small>
                    </div>
                </draggable>
                <h2>Active</h2>
                <draggable :list="active" class="dragArea">
                    <div v-for="deploymentStep in active">
                        {{ deploymentStep.name }} <br> <small>{{ deploymentStep.description }}</small>
                    </div>
                </draggable>
            </div>
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
                inactive:[],
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
                var steps = this.$store.state.sitesStore.deployment_steps;

                this.active = [];
                this.inactive = [];
                _.each(steps, (value) => {
                    if(this.hasStep(value.task)) {
                        this.active.push(value);
                    } else {
                        this.inactive.push(value);
                    }
                });

                return steps;
            },
            currentSiteDeploymentSteps() {
                return this.$store.state.sitesStore.site_deployment_steps;
            }
        }
    }
</script>
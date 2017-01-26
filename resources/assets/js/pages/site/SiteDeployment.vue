<style>
    .dragArea {
        min-height: 20px;
    }
</style>
<template>
    <div v-if="site">
        Site Deployments
        <div @click="addCustomStep" class="btn btn-primary">Add Custom Step</div>
        <form @submit.prevent="updateSiteDeployment">
            <div class="btn btn-primary" @click="selectAllDeployments">Select All</div>
            <div class="btn btn-primary" @click="deselectAllDeployments">Deselect All</div>
            <div class="drag">
                <h2>Inactive</h2>
                <draggable :list="inactive" class="dragArea" :options="{group:'tasks'}" @sort="sortInactiveList">
                    <div v-for="deploymentStep in inactive">
                        <deployment-step-card :deployment-step="deploymentStep"></deployment-step-card>
                    </div>
                </draggable>
                <h2>Active</h2>
                <draggable :list="active" class="dragArea" :options="{group:'tasks'}" @add="sortActiveList">
                    <div v-for="deploymentStep in active">
                        <deployment-step-card :deployment-step="deploymentStep" :key="deploymentStep"></deployment-step-card>
                    </div>
                </draggable>
            </div>

            <div class="btn-footer"><button type="submit" class="btn btn-primary">Update Deployment</button></div>
        </form>
    </div>
</template>

<script>
    import draggable from 'vuedraggable';
    import deploymentStepCard from './components/DeploymentStepCard.vue';
    export default {
        components: {
            draggable,
            deploymentStepCard
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
            '$route': 'fetchData'
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
                                this.active.push(step);
                            } else {
                                step = _.find(possibleDeploymentSteps, { internal_deployment_function : step.internal_deployment_function });
                                if(step) {
                                    this.active.push(step);
                                }
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
            },
            addCustomStep() {
                this.active.push({
                    order: null,
                    script : '',
                    step: "Custom Step",
                    description: "Custom Step",
                })
            },
            sortInactiveList: function(){
                this.$nextTick(function(){
                    this.inactive = _.sortBy(this.inactive, 'order');
                });
            },
            sortActiveList: function(){
                this.$nextTick(function(){
                    this.active = _.sortBy(this.active, 'order');
                });
            },
            deselectAllDeployments() {
                _.each(this.active, (step) => {
                    this.inactive.push(step);
                });

                this.active = [];

                this.sortInactiveList();
            },
            selectAllDeployments() {
                _.each(this.inactive, (step) => {
                    this.active.push(step);
                });

                this.inactive = [];

                this.sortActiveList();
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
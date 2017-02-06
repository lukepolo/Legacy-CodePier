<style>
    .dragArea {
        min-height: 20px;
    }
</style>
<template>
    <div v-if="site">
        <p>
            Here we can customize how we deploy your application. We give you sensible defaults.
            By dragging steps from the inactive to the active we automatically suggest the order.
            Once in the active list you can change the order.
        </p>

        <form @submit.prevent="updateSiteDeployment">
            <div class="col-split col-break-sm">
                <div class="drag">
                    <div class="col">
                        <h3>
                            <tooltip message="We keep steps so you can always put them back into the list. These steps will not be ran durring deployments" class="long">
                                <span class="fa fa-info-circle"></span>
                            </tooltip>
                            Inactive
                            <a class="pull-right" @click="deselectAllDeployments">Deselect All</a>
                        </h3>

                        <draggable :list="inactive" class="dragArea" :options="{group:'tasks'}" @sort="sortInactiveList">
                            <div class="drag-element" v-for="deploymentStep in inactive">
                                <deployment-step-card :deployment-step="deploymentStep"></deployment-step-card>
                            </div>
                        </draggable>
                    </div>
                    <div class="col">
                        <h3>
                            <tooltip message="These are the steps in which we will deploy your applicatioin, they go in order from top to bottom" class="long">
                                <span class="fa fa-info-circle"></span>
                            </tooltip>
                            Active
                            <a class="pull-right" @click="selectAllDeployments">Select All</a>
                        </h3>

                        <draggable :list="active" class="dragArea" :options="{group:'tasks'}" @add="sortActiveList">
                            <div class="drag-element" v-for="deploymentStep in active">
                                <deployment-step-card :deployment-step="deploymentStep" :key="deploymentStep"></deployment-step-card>
                            </div>
                        </draggable>

                        <div class="btn-container text-center">
                            <span @click="addCustomStep" class="btn">Add Custom Step</span>
                        </div>

                    </div>
                </div>
            </div>




            <div class="btn-footer">
                <!-- todo - instead of having to go away from the page and back, give them a quick link to clear.
                I don't care if you update the text on the btn. whatever makes sense -->
                <button class="btn">Clear Changes</button>
                <button type="submit" class="btn btn-primary">Update Deployment</button>
            </div>
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
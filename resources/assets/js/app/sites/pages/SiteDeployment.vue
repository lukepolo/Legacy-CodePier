<template>
    <div v-if="site">
        <p>
            Here we can customize how we deploy your application. We give you sensible defaults.
            By dragging steps from the inactive to the active we automatically suggest the order.
            Once in the active list you can change the order.
        </p>
        <form @submit.prevent="updateSiteDeployment">

            <div class="flyform--heading flyform--heading-sticky">
                <div class="flyform--footer-btns">
                    <button class="btn" @click.prevent="clearChanges">Discard Changes</button>
                    <button type="submit" class="btn btn-primary">Update Deployment</button>
                </div>
            </div>

            <div class="grid-2">
                <div class="flyform--group-checkbox">
                    <label>
                        <input type="checkbox" v-model="form.zero_downtime_deployment" name="zero_downtime_deployment" value="1">
                        <span class="icon"></span>
                        Zero Downtime Deployment
                        <tooltip message="Your app can be deployed in zero d`owntime deployment, we suggest you go for it!"
                                 size="medium">
                            <span class="fa fa-info-circle"></span>
                        </tooltip>
                    </label>
                </div>

                <template v-if="form.zero_downtime_deployment">
                    <div class="flyform--group">
                        <input type="number" v-model="form.keep_releases" name="keep_releases" placeholder=" ">
                        <label for="keep_releases" class="flyform--group-iconlabel">Number of Releases to keep</label>
                        <tooltip
                                message="When using zero downtime deployments you can keep a number of releases, if set to zero we will keep them all"
                                size="medium">
                            <span class="fa fa-info-circle"></span>
                        </tooltip>
                    </div>
                </template>
            </div>

            <div class="col-split col-break-sm">
                <div class="drag">
                    <div class="col">
                        <h3>
                            <tooltip
                                    message="We keep steps so you can always put them back into the list. These steps will not be ran during deployments"
                                    class="long">
                                <span class="fa fa-info-circle"></span>
                            </tooltip>
                            Inactive
                            <a class="pull-right" @click="deselectAllDeployments">Deselect All</a>
                        </h3>

                        <draggable :list="inactive" class="dragArea" :options="{group:'tasks'}"
                                   @sort="sortInactiveList">
                            <div
                                class="drag-element"
                                v-for="(deploymentStep, key) in inactive"
                                v-if="showStep(deploymentStep)"
                            >
                                <deployment-step-card
                                    :deployment-step="deploymentStep"
                                    :suggestedOrder="getSuggestedOrder(deploymentStep)"
                                    v-on:updateStep="updateStep('inactive')"
                                    v-on:deleteStep="deleteStep(key, 'inactive')"
                                ></deployment-step-card>
                            </div>
                        </draggable>
                    </div>
                    <div class="col">
                        <h3>
                            <tooltip
                                    message="These are the steps in which we will deploy your applicatioin, they go in order from top to bottom"
                                    class="long">
                                <span class="fa fa-info-circle"></span>
                            </tooltip>
                            Active
                            <a class="pull-right" @click="selectAllDeployments">Select All</a>
                        </h3>

                        <draggable :list="active" class="dragArea" :options="{group:'tasks'}">
                            <div
                                class="drag-element"
                                v-for="(deploymentStep, key) in active"
                                v-if="showStep(deploymentStep)"
                            >
                                <deployment-step-card
                                    :order="key + 1"
                                    :deployment-step="deploymentStep"
                                    :key="deploymentStep.id"
                                    :suggestedOrder="getSuggestedOrder(deploymentStep)"
                                    v-on:updateStep="updateStep('active')"
                                    v-on:deleteStep="deleteStep(key, 'active')"
                                ></deployment-step-card>
                            </div>
                        </draggable>

                        <div class="btn-container text-center">
                            <span @click="addCustomStep" class="btn btn-small">Add Custom Step</span>
                        </div>

                    </div>
                </div>
            </div>

        </form>
    </div>
</template>

<script>
import draggable from "vuedraggable";
import { DeploymentStepCard } from "../components";

export default {
  components: {
    draggable,
    DeploymentStepCard
  },
  data() {
    return {
      active: [],
      inactive: [],
      form: this.createForm({
        keep_releases: 10,
        zero_downtime_deployment: true,
        site: this.$route.params.site_id
      })
    };
  },
  created() {
    this.fetchData();
    this.siteChange();
  },
  watch: {
    $route: "fetchData",
    site: "siteChange"
  },
  methods: {
    siteChange() {
      this.form.empty();

      let site = this.site;

      this.form.keep_releases = site.keep_releases;
      this.form.zero_downtime_deployment = site.zero_downtime_deployment;

      this.form.setOriginalData();
    },
    fetchData() {
      this.$store
        .dispatch(
          "user_site_deployments/getDeploymentSteps",
          this.$route.params.site_id
        )
        .then(() => {
          this.$store
            .dispatch(
              "user_site_deployments/getSiteDeploymentSteps",
              this.$route.params.site_id
            )
            .then(() => {
              this.clearChanges();
            });
        });
    },
    updateSiteDeployment() {
      this.saveSiteDeploymentConfig();
      this.$store.dispatch("user_site_deployments/updateSiteDeployment", {
        site: this.$route.params.site_id,
        deployment_steps: this.active
      });
    },
    saveSiteDeploymentConfig() {
      this.$store.dispatch(
        "user_site_deployments/updateSiteDeploymentConfig",
        this.form
      );
    },
    hasStep(task) {
      if (this.currentSiteDeploymentSteps.length) {
        return _.find(this.currentSiteDeploymentSteps, {
          internal_deployment_function: task
        });
      }
      return false;
    },
    addCustomStep() {
      let tempId =
        parseInt(this.active.length + 1) + parseInt(this.inactive.length + 1);

      this.active.push({
        id: `temp_${tempId}`,
        order: null,
        script: "",
        step: "Custom Step",
        description: "Custom Step",
        editing: true
      });
    },
    sortInactiveList: function() {
      this.$nextTick(function() {
        this.inactive = _.sortBy(this.inactive, "order");
      });
    },
    deselectAllDeployments() {
      _.each(this.active, step => {
        this.inactive.push(step);
      });

      this.active = [];

      this.sortInactiveList();
    },
    selectAllDeployments() {
      _.each(this.inactive, step => {
        this.active.push(step);
      });

      this.inactive = [];
    },
    clearChanges() {
      this.active = [];
      this.inactive = [];

      _.each(this.currentSiteDeploymentSteps, step => {
        if (step.script) {
          step.editing = false;
        }
        this.active.push(step);
      });

      _.each(this.deploymentSteps, step => {
        if (!this.hasStep(step.internal_deployment_function)) {
          this.inactive.push(step);
        }
      });
    },
    updateStep(state) {
      this[state] = Object.assign([], this[state], _.cloneDeep(this[state]));
    },
    deleteStep(deploymentStep, state) {
      this[state].splice(deploymentStep, 1);
    },
    getSuggestedOrder(deploymentStep) {
      let internalStep = this.internalStep(deploymentStep);
      if (internalStep) {
        let activeSteps = _.filter(this.deploymentSteps, step => {
          return _.find(this.active, { step: step.step });
        });

        let steps = _.filter(activeSteps, step => {
          return step.order < internalStep.order;
        });

        return steps.length + 1;
      }

      return null;
    },
    internalStep(deploymentStep) {
      if (deploymentStep.internal_deployment_function && this.deploymentSteps) {
        return _.find(this.deploymentSteps, step => {
          return (
            step.internal_deployment_function ===
            deploymentStep.internal_deployment_function
          );
        });
      }

      return false;
    },
    showStep(deploymentStep) {
      if (
        !this.showZeroDowntimeDeploymentOptions &&
        this.isZeroTimeDeploymentStep(deploymentStep)
      ) {
        return false;
      }
      return true;
    },
    isZeroTimeDeploymentStep(deploymentStep) {
      let step = this.internalStep(deploymentStep);
      if (step) {
        return step.zero_downtime_deployment;
      }
      return false;
    }
  },
  computed: {
    site() {
      return this.$store.state.user_sites.site;
    },
    deploymentSteps() {
      return this.$store.state.user_site_deployments.deployment_steps.map(
        (value, index) => {
          value.id = `temp_${index}`;
          return value;
        }
      );
    },
    currentSiteDeploymentSteps() {
      return this.$store.state.user_site_deployments.site_deployment_steps;
    },
    showZeroDowntimeDeploymentOptions() {
      return this.form.zero_downtime_deployment;
    }
  }
};
</script>

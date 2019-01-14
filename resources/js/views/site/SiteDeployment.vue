<template>
  <div v-if="site">
    <p>
      Here we can customize how we deploy your site. We give you sensible
      defaults. By dragging steps from the inactive to the active we
      automatically suggest the order. Once in the active list you can change
      the order.
    </p>
    <base-form v-form="form" :action="updateSiteDeployment">
      <div class="flyform--heading flyform--heading-sticky">
        <div class="flyform--footer-btns">
          <button class="btn" @click.prevent="clearChanges">
            Discard Changes
          </button>
          <button type="submit" class="btn btn-primary">
            Update Deployment
          </button>
        </div>
      </div>

      <div class="grid-2">
        <base-checkbox
          :disabled="site.type === 'Swift'"
          name="zero_downtime_deployment"
          label="Zero Downtime Deployment"
          v-model="form.zero_downtime_deployment"
          tooltip="Your app can be deployed in zero downtime deployment, we suggest you go for it!"
        ></base-checkbox>

        <template v-if="form.zero_downtime_deployment">
          <base-input
            validate
            name="keep_releases"
            label="Number of Releases to keep"
            v-model="form.keep_releases"
            tooltip="When using zero downtime deployments you can keep a number of releases, if set to zero we will keep them all"
          ></base-input>
        </template>
      </div>

      <div class="col-split col-break-sm">
        <div class="drag">
          <div class="col">
            <h3>
              <tooltip
                message="We keep steps so you can always put them back into the list. These steps will not be ran during deployments"
                class="long"
              >
                <span class="fa fa-info-circle"></span>
              </tooltip>
              Inactive
              <a class="pull-right" @click="deselectAllDeployments"
                >Deselect All</a
              >
            </h3>

            <draggable
              class="dragArea"
              :list="inactive"
              :options="{ group: 'tasks' }"
              @sort="sortInactiveList"
            >
              <div
                class="drag-element"
                v-for="(deploymentStep, key) in inactive"
                v-if="shouldShowStep(deploymentStep)"
              >
                <deployment-step-card
                  v-model="inactive[key]"
                  :suggestedOrder="getSuggestedOrder(deploymentStep)"
                  v-on:deleteStep="deleteStep(key, 'inactive')"
                ></deployment-step-card>
              </div>
            </draggable>
          </div>

          <div class="col">
            <h3>
              <tooltip
                message="These are the steps in which we will deploy your applicatioin, they go in order from top to bottom"
                class="long"
              >
                <span class="fa fa-info-circle"></span>
              </tooltip>
              Active
              <a class="pull-right" @click="selectAllDeployments">Select All</a>
            </h3>

            <draggable
              class="dragArea"
              :list="active"
              :options="{ group: 'tasks' }"
            >
              <div
                class="drag-element"
                v-for="(deploymentStep, key) in active"
                v-if="shouldShowStep(deploymentStep)"
              >
                <deployment-step-card
                  :order="key + 1"
                  v-model="active[key]"
                  :key="deploymentStep.id"
                  :suggestedOrder="getSuggestedOrder(deploymentStep)"
                  v-on:deleteStep="deleteStep(key, 'active')"
                ></deployment-step-card>
              </div>
            </draggable>

            <div class="btn-container text-center">
              <span @click="addCustomStep" class="btn btn-small"
                >Add Custom Step</span
              >
            </div>
          </div>
        </div>
      </div>

      <div>
        <hr />
        <h3>
          <tooltip
            class="long"
            message="ex: If you have two servers running deployments, these tasks will only be ran after both of those have been completed."
          >
            <span class="fa fa-info-circle"></span>
          </tooltip>
          Tasks To Be Run After Successful Deployment
        </h3>
        <draggable
          class="dragArea"
          :list="afterDeployment"
          :options="{ group: 'tasks' }"
        >
          <div
            class="drag-element"
            v-for="(deploymentStep, key) in afterDeployment"
            v-if="shouldShowStep(deploymentStep)"
          >
            <deployment-step-card
              :order="key + 1"
              v-model="afterDeployment[key]"
              v-on:deleteStep="deleteStep(key, 'active')"
            ></deployment-step-card>
          </div>
        </draggable>
      </div>
    </base-form>
  </div>
</template>

<script>
import draggable from "vuedraggable";
import DeploymentStepCard from "./components/site-deployment-components/DeploymentStepCard";

export default {
  components: {
    draggable,
    DeploymentStepCard,
  },
  data() {
    return {
      active: [],
      inactive: [],
      afterDeployment: [],
      form: this.createForm({
        keep_releases: 10,
        zero_downtime_deployment: true,
      }),
    };
  },
  watch: {
    $route: {
      immediate: true,
      async handler() {
        await this.$store.dispatch(
          "user/sites/deployments/getDeploymentSteps",
          {
            site: this.siteId,
          },
        );
        await this.$store.dispatch(
          "user/sites/deployments/getAvailableDeploymentSteps",
          {
            site: this.siteId,
          },
        );
        this.clearChanges();
      },
    },
    site: {
      immediate: true,
      handler(site) {
        this.form.initial();
        this.form.fill({
          keep_releases: site.keep_releases,
          zero_downtime_deployment: site.zero_downtime_deployment,
        });
        this.form.setAsOriginalData();
      },
    },
  },
  methods: {
    updateSiteDeployment() {
      this.saveSiteDeploymentConfig();
      this.$store.dispatch("user/sites/deployments/updateDeployment", {
        parameters: {
          site: this.siteId,
        },
        data: {
          deployment_steps: this.active.concat(
            this.afterDeployment.map((step) => {
              step.after_deploy = true;
              return step;
            }),
          ),
        },
      });
    },
    saveSiteDeploymentConfig() {
      this.$store.dispatch("user/sites/deployments/create", {
        parameters: {
          site: this.siteId,
        },
        data: this.form.data(),
      });
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
        editing: true,
      });
    },
    sortInactiveList: function() {
      this.$nextTick(function() {
        this.inactive.sort((a, b) => {
          return a.order - b.order;
        });
      });
    },
    deselectAllDeployments() {
      this.active.forEach((step) => {
        this.inactive.push(step);
      });
      this.active = [];
      this.sortInactiveList();
    },
    selectAllDeployments() {
      this.inactive.forEach((step) => {
        this.active.push(step);
      });
      this.inactive = [];
    },
    clearChanges() {
      this.active = Object.assign([], this.activeDeploymentSteps);
      this.inactive = Object.assign([], this.inactiveDeploymentSteps);
      this.afterDeployment = Object.assign([], this.afterDeploymentSteps);
    },
    deleteStep(deploymentStep, state) {
      // this[state].splice(deploymentStep, 1);
    },
    getSuggestedOrder(deploymentStep) {
      let internalStep = this.getInternalStep(deploymentStep);
      if (internalStep) {
        let steps = this.active.filter((step) => {
          return this.getInternalStep(step) && step.order < internalStep.order;
        });
        return steps.length + 1;
      }
      return null;
    },
    getInternalStep(deploymentStep) {
      if (deploymentStep.internal_deployment_function) {
        return this.availableDeploymentSteps.find((step) => {
          return (
            step.internal_deployment_function ===
            deploymentStep.internal_deployment_function
          );
        });
      }
      return false;
    },
    shouldShowStep(step) {
      if (!this.form.zero_downtime_deployment) {
        let internalStep = this.getInternalStep(step);
        if (internalStep && internalStep.zero_downtime_deployment) {
          return false;
        }
      }
      return true;
    },
  },
  computed: {
    site() {
      return this.$store.getters["user/sites/show"](this.siteId);
    },
    siteId() {
      return this.$route.params.site;
    },
    deploymentSteps() {
      return this.$store.state.user.sites.deployments.deployment_steps;
    },
    availableDeploymentSteps() {
      return this.$store.state.user.sites.deployments
        .available_deployment_steps;
    },
    activeDeploymentSteps() {
      return this.deploymentSteps.filter((step) => {
        return !step.after_deploy;
      });
    },
    inactiveDeploymentSteps() {
      return this.availableDeploymentSteps.filter((availableStep) => {
        return !this.deploymentSteps.find((step) => {
          return (
            step.internal_deployment_function ===
            availableStep.internal_deployment_function
          );
        });
      });
    },
    afterDeploymentSteps() {
      return this.deploymentSteps.filter((step) => {
        return step.after_deploy;
      });
    },
  },
};
</script>

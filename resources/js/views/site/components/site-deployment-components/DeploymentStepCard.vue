<template>
  <div>
    <template v-if="!deploymentStep.internal_deployment_function">
      <a class="text-error pull-right" @click="deleteStep"
        ><span class="icon-trash"></span
      ></a>
      <a class="text-success pull-right" @click="edit"
        ><span class="icon-pencil"></span
      ></a>
    </template>

    <div class="drag-name">
      <!--<server-selection-->
      <!--:showModal="deploymentStep.showModal"-->
      <!--:title="`${order}. ${deploymentStep.step}`"-->
      <!--:availableServerTypes="availableServerTypes"-->
      <!--:server_ids.sync="server_ids"-->
      <!--:server_types.sync="server_types"-->
      <!--&gt;</server-selection>-->
      <tooltip
        v-if="deploymentStep.internal_deployment_function"
        :message="'Suggested order ' + suggestedOrder"
        class="pull-right"
        placement="top-left"
      >
        <span class="fa fa-info-circle"></span>
      </tooltip>
      <span v-if="order">{{ order }}.</span> {{ deploymentStep.step }}
    </div>

    <div class="small">
      {{
        deploymentStep.internal_deployment_function
          ? deploymentStep.internal_deployment_function.description
          : deploymentStep.description
      }}
    </div>

    <base-form v-form="form" :action="save" v-if="isEditing">
      <base-input
        v-model="form.step"
        name="step"
        label="Step Name"
      ></base-input>

      <base-input
        v-model="form.script"
        name="script"
        label="Script"
      ></base-input>

      <div class="flyform--footer-btns">
        <span class="btn btn-small" @click="cancel">Cancel</span>
        <button class="btn btn-primary btn-small">Save</button>
      </div>
    </base-form>
  </div>
</template>

<script>
// import { ServerSelection } from "./../../setup/components";

export default {
  components: {
    // ServerSelection
  },
  props: {
    deploymentStep: {
      required: true,
    },
    order: {
      required: false,
    },
    suggestedOrder: {
      required: false,
    },
  },
  data({ deploymentStep }) {
    return {
      form: this.createForm({
        step: deploymentStep.step,
        script: deploymentStep.script,
        // server_ids: deploymentStep.server_ids || [],
        // server_types: deploymentStep.server_types || []
      }),
      isEditing: deploymentStep.editing || false,
    };
  },
  watch: {
    step() {
      this.deploymentStep.step = this.step;
    },
    script() {
      this.deploymentStep.script = this.script;
    },
    // server_ids() {
    //   this.deploymentStep.server_ids = this.server_ids;
    // },
    // server_types() {
    //   this.deploymentStep.server_types = this.server_types;
    // }
  },
  methods: {
    edit() {
      this.isEditing = true;
    },
    save() {
      this.$emit(
        "update:deploymentStep",
        Object.assign({}, this.deploymentStep, this.form.data()),
      );
    },
    cancel() {
      this.form.reset();
      this.isEditing = false;
    },
    deleteStep() {
      this.$emit("deleteStep");
    },
  },
  computed: {
    siteServers() {
      return this.$store.getters["user_site_servers/getServers"](
        this.$route.params.site_id,
      );
    },
    displayServerSelection() {
      return false;
      // if (this.$route.params.site_id) {
      //   if (this.siteServers && this.siteServers.length > 1) {
      //     return true;
      //   }
      //   return false;
      // }
    },
    availableServerTypes() {
      // return _.pickBy(window.Laravel.serverTypes, function(serverType) {
      //   return (
      //     serverType === "web" ||
      //     serverType === "full_stack" ||
      //     serverType === "worker"
      //   );
      // });
    },
  },
};
</script>

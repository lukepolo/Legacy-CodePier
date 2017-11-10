<template>
  <div>
      <template v-if="internalStep || deploymentStep.editing === false">

          <template v-if="!internalStep">
              <a class="text-error pull-right" @click="deleteStep"><span class="icon-trash"></span></a>
              <a class="text-success pull-right" @click="edit"><span class="icon-pencil"></span></a>
          </template>

          <div class="drag-name">
              <tooltip
                  v-if="internalStep"
                  :message="'Suggested order ' + suggestedOrder"
                  class="pull-right"
                  placement="top-left"
              >
                  <span class="fa fa-info-circle"></span>
              </tooltip>
              <span v-if="order">{{ order }}.</span> {{ deploymentStep.step }}
              <server-selection :availableServerTypes="availableServerTypes" :server_ids.sync="server_ids" :server_types.sync="server_types"></server-selection>

          </div>

          <div class="small">{{ internalStep ? internalStep.description : deploymentStep.description }}</div>

      </template>

      <template v-else>

        <form @submit.prevent>
            <div class="flyform--group">
                <input type="text" name="step" v-model="step" placeholder=" ">
                <label for="step">Step Name</label>
            </div>

            <div class="flyform--group">
                <input type="text" name="script" v-model="script" placeholder=" ">
                <label for="script">Script</label>
            </div>

            <div class="flyform--footer-btns">
                <a class="btn btn-small" @click="cancel">Cancel</a>
                <a class="btn btn-primary btn-small" @click="save">Save</a>
            </div>
        </form>
      </template>

  </div>

</template>

<script>

    import { ServerSelection } from "./../../setup/components"

    export default {
        components : {
            ServerSelection
        },
        props : ['deploymentStep', 'order', 'suggestedOrder'],
        data() {
            return {
                step : this.deploymentStep.step,
                script : this.deploymentStep.script,
                server_ids : this.deploymentStep.server_ids ? this.deploymentStep.server_ids : [],
                server_types : this.deploymentStep.server_types ? this.deploymentStep.server_types : [],
            }
        },
        watch : {
            'step' : function() {
                this.deploymentStep.step = this.step;
            },
            'script' : function() {
                this.deploymentStep.script = this.script;
            },
            'server_ids' : function() {
                this.deploymentStep.server_ids = this.server_ids
            },
            'server_types' : function() {
                this.deploymentStep.server_types = this.server_types
            }
        },
        methods: {
            edit() {
                this.deploymentStep.editing = true
                this.updateStep()
            },
            save() {
                this.deploymentStep.editing = false
                this.deploymentStep.step = this.step
                this.deploymentStep.script = this.script
                this.deploymentStep.server_ids = this.server_ids
                this.deploymentStep.description = this.description
                this.updateStep()
            },
            cancel() {
                this.deploymentStep.editing = false
                this.updateStep()
            },
            updateStep() {
                this.$emit('updateStep', this.deploymentStep)
            },
            deleteStep() {
                this.$emit('deleteStep')
            }
        },
        computed: {
            siteServers() {
                return this.$store.getters['user_site_servers/getServers'](this.$route.params.site_id)
            },
            displayServerSelection() {
                if(this.$route.params.site_id) {
                    if(this.siteServers && this.siteServers.length > 1) {
                        return true
                    }
                    return false
                }
            },
            availableServerTypes() {
                return _.pickBy(window.Laravel.serverTypes, function(serverType) {
                    return serverType === 'web' || serverType === 'full_stack' || serverType === 'worker'
                })
            },
            deploymentSteps() {
                return this.$store.state.user_site_deployments.deployment_steps;
            },
            internalStep() {
                if(this.deploymentStep.internal_deployment_function && this.deploymentSteps) {
                    return _.find(this.deploymentSteps, (step) => {
                        return step.internal_deployment_function === this.deploymentStep.internal_deployment_function
                    })
                }

                return false
            }
        }
    }
</script>
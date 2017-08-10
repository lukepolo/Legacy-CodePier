<template>
  <div>
      <template v-if="deploymentStep.internal_deployment_function || deploymentStep.editing === false">

          <template v-if="!deploymentStep.internal_deployment_function">
              <a class="text-error pull-right" @click="deleteStep"><span class="icon-trash"></span></a>
              <a class="text-success pull-right" @click="edit"><span class="icon-pencil"></span></a>
          </template>

          <div class="drag-name">
              <tooltip
                  v-if="deploymentStep.internal_deployment_function"
                  :message="'Suggested order ' + deploymentStep.order"
                  class="pull-right"
                  placement="top-left"
              >
                  <span class="fa fa-info-circle"></span>
              </tooltip>
              {{ deploymentStep.step }}

              <server-selection :availableServerTypes="availableServerTypes" :servers.sync="servers" :server_types.sync="server_types"></server-selection>

          </div>

          <div class="small">{{ deploymentStep.description }}</div>

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
        props : ['deploymentStep'],
        data() {
            return {
                step : this.deploymentStep.step,
                script : this.deploymentStep.script,
                servers : this.deploymentStep.servers ? this.deploymentStep.servers : [],
                server_types : this.deploymentStep.servers ? this.deploymentStep.server_types : [],
            }
        },
        watch : {
            'step' : function() {
                this.deploymentStep.step = this.step;
            },
            'script' : function() {
                this.deploymentStep.script = this.script;
            },
            'servers' : function() {
                this.deploymentStep.servers = this.servers
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
                this.deploymentStep.servers = this.servers
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
            }
        }
    }
</script>
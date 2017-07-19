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

            <div class="flyform--group">
                <label>Description</label>
                <textarea rows="2" v-model="description"></textarea>
            </div>

            <div class="flyform--footer-btns">
                <!--<a class="btn btn-danger btn-small pull-left"><span class="icon-cancel"></span></a>-->
                <a class="btn btn-small" @click="cancel">Cancel</a>
                <a class="btn btn-primary btn-small" @click="save">Save</a>
            </div>
        </form>

      </template>

  </div>

</template>

<script>
    export default {
        props : ['deploymentStep'],
        data() {
            return {
                step : this.deploymentStep.step,
                script : this.deploymentStep.script,
                description : this.deploymentStep.description
            }
        },
        watch : {
            'step' : function() {
                this.deploymentStep.step = this.step;
            },
            'script' : function() {
                this.deploymentStep.script = this.script;
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
        }
    }
</script>
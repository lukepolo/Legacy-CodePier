<template>
  <div>
<<<<<<< HEAD

      <template v-if="deploymentStep.internal_deployment_function || deploymentStep.editing == false">

          <template v-if="!deploymentStep.internal_deployment_function">
              <a class="text-error pull-right" @click="deleteStep"><span class="icon-cancel"></span></a>
              <a class="text-success pull-right" @click="edit"><span class="fa fa-edit"></span></a>
          </template>
=======
      <template v-if="deploymentStep.internal_deployment_function">
          <!-- todo - allow them to delete custom steps
           -- Note: this should no show up unless it is custom -->
          <!-- todo jf -- switch out icon -->
          <a class="text-error pull-right"><span class="icon-cancel"></span></a>
>>>>>>> ac6436126d95122e4fdb99d5e1555263f35f3d16

          <div class="drag-name">
              {{ deploymentStep.step }}
          </div>

          <div class="small">{{ deploymentStep.description }}</div>

      </template>

      <template v-else>

          <div class="jcf-form-wrap">

              <form @submit.prevent class="floating-labels">

                  <div class="jcf-input-group">
                      <input type="text" name="step" v-model="step">
                      <label for="step">
                          <span class="float-label">Step Name</span>
                      </label>
                  </div>

                  <div class="jcf-input-group">
                      <input type="text" name="script" v-model="script">
                      <label for="script">
                          <span class="float-label">Script</span>
                      </label>
                  </div>

                  <div class="jcf-input-group">
                      <div class="input-question" >Description</div>
                      <textarea rows="2" v-model="description"></textarea>
                  </div>

                  <div class="btn-footer">
<<<<<<< HEAD
                      <a class="btn btn-small" @click="cancel">Cancel</a>
                      <a class="btn btn-primary btn-small" @click="save">Save</a>
=======
                      <!-- todo - have save button get it out of edit mode and into a component like the rest -->
                      <!-- todo - cancel should clear form and close -->
                      <a class="btn btn-danger btn-small pull-left"><span class="icon-cancel"></span></a>
                      <a class="btn btn-small">Cancel</a>
                      <a class="btn btn-primary btn-small">Save</a>
>>>>>>> ac6436126d95122e4fdb99d5e1555263f35f3d16
                  </div>
              </form>

          </div>

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
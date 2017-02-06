<template>
  <div>
      <template v-if="deploymentStep.internal_deployment_function">
          <!-- todo - allow them to delete custom steps
           -- Note: this should no show up unless it is custom -->
          <!--<a class="text-error pull-right"><span class="icon-cancel"></span></a>-->

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
                      <div class="input-question">Description</div>
                      <textarea rows="2"></textarea>
                  </div>

                  <div class="btn-footer">
                      <a class="btn btn-danger btn-small pull-left"><span class="icon-cancel"></span> </a>
                      <a class="btn btn-small">Cancel</a>
                      <a class="btn btn-primary btn-small">Save</a>
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
                script : this.deploymentStep.script
            }
        },
        watch : {
            'step' : function() {
                this.deploymentStep.step = this.step;
            },
            'script' : function() {
                this.deploymentStep.script = this.script;
            }
        }
    }
</script>
<template>
  <section>
      <template v-if="deploymentStep.internal_deployment_function">
          {{ deploymentStep.step }} <br> <small>{{ deploymentStep.description }}</small>
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
              </form>
          </div>
      </template>
  </section>
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
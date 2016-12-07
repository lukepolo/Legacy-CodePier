<template>
  <section>
      <template v-if="deploymentStep.internal_deployment_function">
          {{ deploymentStep.step }} <br> <small>{{ deploymentStep.description }}</small>
      </template>
      <template v-else>
          <input type="text" name="step" v-model="step">
          <textarea name="script" v-model="script"></textarea>
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
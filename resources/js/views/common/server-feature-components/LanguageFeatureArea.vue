<template>
  <div>
    <div v-for="(features, framework) in frameworkFeatures">
      <template v-if="features && Object.keys(features).length">
        <h3>{{ framework }}</h3>
        <feature-area
          :features="features"
          v-model="selectedServerFeatures"
        ></feature-area>
      </template>
    </div>
  </div>
</template>

<script>
import FeatureArea from "./FeatureArea";
export default {
  components: { FeatureArea },
  props: {
    section: {
      required: true,
    },
    value: {
      required: true,
    },
  },
  computed: {
    selectedServerFeatures: {
      get() {
        return this.value;
      },
      set(value) {
        this.$emit("input", value);
      },
    },
    frameworkFeatures() {
      let frameworkFeatures = this.availableFrameworks[this.section];
      if (frameworkFeatures && Object.keys(frameworkFeatures).length) {
        return frameworkFeatures;
      }
    },
    availableFrameworks() {
      return this.$store.state.server.frameworks.frameworks;
    },
  },
};
</script>

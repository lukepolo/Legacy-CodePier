<template>
  <div v-for="(features, framework) in frameworkFeatures(section)">
    <template v-if="features && Object.keys(features).length">
      <h1>{{ framework }} -- {{ section }}</h1>
      <feature-area
        :features="features"
        v-model="selectedServerFeatures"
      ></feature-area>
    </template>
  </div>
</template>

<script>
import ServerFeatures from "@views/common/ServerFeatures";
import ServerFeature from "@views/common/server-feature-components/ServerFeature";
export default {
  components: { ServerFeature, ServerFeatures },
  props: {
    features: {
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
  },
};
</script>

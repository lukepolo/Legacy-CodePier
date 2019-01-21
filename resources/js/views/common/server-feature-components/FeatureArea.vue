<template>
  <section>
    <template v-for="feature in features">
      <div class="flyform--group-checkbox action-right">
        <!--<pre>{{ feature }}</pre>-->
        <server-feature
          :feature="feature"
          v-model="selectedServerFeatures"
        ></server-feature>

        <!--<template v-if="frameworks">-->
        <!--<hr />-->
        <!--<div class="flyform&#45;&#45;group">-->
        <!--<h3>Frameworks Features for {{ area }}</h3>-->
        <!--</div>-->
        <!--<feature-area-->
        <!--:server="server"-->
        <!--:area="framework"-->
        <!--:features="features"-->
        <!--:selected_server_features="selected_server_features"-->
        <!--v-for="(features, framework) in getFrameworks(area)"-->
        <!--:key="framework"-->
        <!--&gt;-->
        <!--</feature-area>-->
        <!--</template>-->
      </div>
    </template>
  </section>
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

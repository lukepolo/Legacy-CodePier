<template>
  <section>
    <div class="tab-container tab-left tab-left-small">
      <ul class="nav nav-tabs">
        <template
          v-for="(features, serverFeatureArea) in availableServerFeatures"
        >
          <feature-title :feature="serverFeatureArea"></feature-title>
        </template>
        <template
          v-for="(features, serverFeatureArea) in availableServerLanguages"
        >
          <feature-title :feature="serverFeatureArea"></feature-title>
        </template>
      </ul>

      <div class="tab-content">
        <div role="tabpanel" class="tab-pane active">
          <!--<feature-area-->
          <!--:server="server"-->
          <!--:selected_server_features="serverFeatures"-->
          <!--:area="serverFeatureArea"-->
          <!--:features="features"-->
          <!--v-for="(features, serverFeatureArea) in availableServerFeatures"-->
          <!--v-show="section === serverFeatureArea"-->
          <!--:current_selected_features="currentSelectedFeatures"-->
          <!--v-on:featuresChanged="updateSelectedFeatures"-->
          <!--:key="serverFeatureArea"-->
          <!--&gt;</feature-area>-->
          <!--<feature-area-->
          <!--:server="server"-->
          <!--:selected_server_features="serverFeatures"-->
          <!--:area="serverLanguageArea"-->
          <!--:features="features"-->
          <!--:frameworks="true"-->
          <!--v-for="(features, serverLanguageArea) in availableServerLanguages"-->
          <!--v-show="section === serverLanguageArea"-->
          <!--:current_selected_features="currentSelectedFeatures"-->
          <!--v-on:featuresChanged="updateSelectedFeatures"-->
          <!--:key="serverLanguageArea"-->
          <!--&gt;</feature-area>-->
        </div>
      </div>
    </div>
  </section>
</template>

<script>
import FeatureArea from "./server-feature-components/FeatureArea";
import FeatureTitle from "./server-feature-components/FeatureTitle";

export default {
  model: {
    prop: "selectedServerFeatures",
  },
  components: {
    FeatureArea,
    FeatureTitle,
  },
  props: {
    selectedServerFeatures: {
      required: true,
    },
  },
  data() {
    return {
      section: null,
      currentSelectedFeatures: null,
    };
  },
  created() {
    this.$router.push({
      name: "site.server-features",
      params: {
        section: Object.keys(this.availableServerFeatures)[0],
      },
    });
  },
  watch: {
    $route: {
      immediate: true,
      handler() {
        this.$store.dispatch("server/features/get");
        this.$store.dispatch("server/languages/get");
        this.$store.dispatch("server/frameworks/get");
      },
    },
  },
  methods: {
    updateSelectedFeatures(feature, enabled) {
      // if (!this.currentSelectedFeatures[feature.service]) {
      //   this.$set(this.currentSelectedFeatures, feature.service, {});
      // }
      //
      // let areaFeatures = this.currentSelectedFeatures[feature.service];
      //
      // if (!_.has(areaFeatures, feature.input_name)) {
      //   this.$set(areaFeatures, feature.input_name, { enabled: enabled });
      // } else {
      //   this.$set(areaFeatures[feature.input_name], "enabled", enabled);
      // }
    },
  },
  computed: {
    availableServerLanguages() {
      return this.$store.state.server.languages.languages;
    },
    availableServerFrameworks() {
      return this.$store.state.server.frameworks.frameworks;
    },
    availableServerFeatures() {
      return this.$store.state.server.features.features;
    },
  },
};
</script>

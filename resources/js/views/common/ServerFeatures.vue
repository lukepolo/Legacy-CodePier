<template>
  <section>
    <div class="tab-container tab-left tab-left-small">
      <ul class="nav nav-tabs">
        <template
          v-for="(features, serverFeatureArea) in availableServerFeatures"
        >
          <template v-if="hasFeatures(features)">
            <feature-title
              :feature="serverFeatureArea"
              :route-name="routeName"
            ></feature-title>
            <portal to="feature-areas">
              <feature-area
                :features="features"
                v-model="selectedServerFeatures"
                v-show="$route.params.section === serverFeatureArea"
              ></feature-area>
            </portal>
          </template>
        </template>
        <template
          v-for="(features, serverFeatureArea) in availableServerLanguages"
        >
          <template v-if="hasFeatures(features)">
            <feature-title
              :feature="serverFeatureArea"
              :route-name="routeName"
            ></feature-title>
            <portal to="feature-areas">
              <feature-area
                :features="features"
                v-model="selectedServerFeatures"
                v-show="$route.params.section === serverFeatureArea"
              ></feature-area>
            </portal>
          </template>
        </template>
      </ul>

      <div class="tab-content">
        <div role="tabpanel" class="tab-pane active">
          <portal-target name="feature-areas" :multiple="true"></portal-target>
          <!--:server="server"-->
          <!--:selected_server_features="serverFeatures"-->
          <!--:current_selected_features="currentSelectedFeatures"-->
          <!--&gt;</feature-area>-->

          <!--<feature-area-->
          <!--:server="server"-->
          <!--:selected_server_features="serverFeatures"-->
          <!--:frameworks="true"-->
          <!--:current_selected_features="currentSelectedFeatures"-->
        </div>
      </div>
    </div>
  </section>
</template>

<script>
import FeatureArea from "./server-feature-components/FeatureArea";
import FeatureTitle from "./server-feature-components/FeatureTitle";

export default {
  provide() {
    return {
      editable: this.editable,
    };
  },
  components: {
    FeatureArea,
    FeatureTitle,
  },
  props: {
    value: {
      required: true,
    },
    editable: {
      default: true,
      required: false,
    },
    routeName: {
      required: true,
    },
  },
  data() {
    return {
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
    hasFeatures(features) {
      return Object.keys(features).length;
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

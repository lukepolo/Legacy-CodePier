<template>
    <section>
        <div class="tab-container tab-left tab-left-small">

            <ul class="nav nav-tabs">

                <template v-for="(features, serverFeatureArea) in availableServerFeatures">
                    <li :class="{ 'router-link-active' : section === serverFeatureArea }" v-if="features.length !==  0">
                        <a @click="switchSection(serverFeatureArea)">
                            {{ getSectionTitle(serverFeatureArea) }}
                        </a>
                    </li>
                </template>

                <template v-for="(features, serverLanguageArea) in availableServerLanguages">
                    <li :class="{ 'router-link-active' : section === serverLanguageArea }" v-if="features.length !==  0">
                        <a @click="switchSection(serverLanguageArea)">
                            {{ getSectionTitle(serverLanguageArea) }}
                        </a>
                    </li>
                </template>

            </ul>

            <div class="tab-content">

                <div role="tabpanel" class="tab-pane active">

                    <feature-area
                        :server="server"
                        :selected_server_features="serverFeatures"
                        :area="serverFeatureArea"
                        :features="features"
                        v-for="(features, serverFeatureArea) in availableServerFeatures"
                        v-show="section === serverFeatureArea"
                        :current_selected_features="currentSelectedFeatures"
                        v-on:featuresChanged="updateSelectedFeatures"
                        :key="serverFeatureArea"
                    ></feature-area>
                    <feature-area
                        :server="server"
                        :selected_server_features="serverFeatures"
                        :area="serverLanguageArea"
                        :features="features"
                        :frameworks="true"
                        v-for="(features, serverLanguageArea) in availableServerLanguages"
                        v-show="section === serverLanguageArea"
                        :current_selected_features="currentSelectedFeatures"
                        v-on:featuresChanged="updateSelectedFeatures"
                        :key="serverLanguageArea"
                    ></feature-area>

                </div>

            </div>

        </div>
    </section>
</template>

<script>
import FeatureArea from "./FeatureArea";

export default {
  components: {
    FeatureArea
  },
  created() {
    this.fetchData();
  },
  watch: {
    $route: "fetchData"
  },
  data() {
    return {
      section: null,
      currentSelectedFeatures: null
    };
  },
  methods: {
    fetchData() {
      if (this.siteId) {
        this.$store.dispatch("user_site_server_features/get", {
          site: this.siteId,
          server_type: this.$route.params.type
        });
      }

      if (this.serverId) {
        this.$store.dispatch("user_server_features/get", this.serverId);
      }

      this.$store.dispatch("server_features/get");
      this.$store.dispatch("server_languages/get");
      this.$store.dispatch("server_frameworks/get");
    },
    getSectionTitle: function(area) {
      let areaName = area;
      if (/[a-z]/.test(area)) {
        areaName = area.replace(/([A-Z].*)(?=[A-Z]).*/g, "$1");
      }
      return areaName + " Features";
    },
    switchSection: function(area) {
      Vue.set(this, "section", area);
    },
    updateSelectedFeatures(feature, enabled) {
      if (!this.currentSelectedFeatures[feature.service]) {
        Vue.set(this.currentSelectedFeatures, feature.service, {});
      }

      let areaFeatures = this.currentSelectedFeatures[feature.service];

      if (!_.has(areaFeatures, feature.input_name)) {
        Vue.set(areaFeatures, feature.input_name, { enabled: enabled });
      } else {
        Vue.set(areaFeatures[feature.input_name], "enabled", enabled);
      }
    }
  },
  computed: {
    siteId() {
      return this.$route.params.site_id;
    },
    server() {
      if (this.serverId) {
        return this.$store.state.user_servers.server;
      }
    },
    serverId() {
      return this.$route.params.server_id;
    },
    serverFeatures() {
      let serverFeatures = {};
      if (this.siteId) {
        serverFeatures = this.$store.state.user_site_server_features.features;
      }
      if (this.serverId) {
        return this.$store.state.user_server_features.features;
      }

      this.currentSelectedFeatures = serverFeatures;

      return serverFeatures;
    },
    availableServerFeatures() {
      let serverFeatures = this.$store.state.server_features.features;

      this.section = _.keys(serverFeatures)[0];

      return serverFeatures;
    },
    availableServerLanguages() {
      return this.$store.state.server_languages.languages;
    },
    availableServerFrameworks() {
      return this.$store.state.server_frameworks.frameworks;
    }
  }
};
</script>

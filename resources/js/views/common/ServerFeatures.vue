<template>
  <section>
    <div class="tab-container tab-left tab-left-small">
      <ul class="nav nav-tabs">
        <template v-for="(features, section) in availableServerFeatures">
          <template v-if="hasFeatures(features)">
            <feature-title
              :section="section"
              :route-name="routeName"
            ></feature-title>
            <portal to="feature-areas">
              <feature-area
                :features="features"
                v-model="selectedServerFeatures"
                v-show="$route.params.section === section"
              ></feature-area>
            </portal>
          </template>
        </template>
        <template v-for="(features, section) in availableLanguages">
          <template v-if="hasFeatures(features)">
            <feature-title
              :section="section"
              :route-name="routeName"
            ></feature-title>

            <portal to="feature-areas">
              <div v-show="$route.params.section === section">
                <feature-area
                  :features="features"
                  v-model="selectedServerFeatures"
                ></feature-area>
                <language-feature-area
                  :section="section"
                  v-model="selectedServerFeatures"
                ></language-feature-area>
              </div>
            </portal>
          </template>
        </template>
      </ul>

      <div class="tab-content">
        <div role="tabpanel" class="tab-pane active">
          <portal-target name="feature-areas" :multiple="true"></portal-target>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
import FeatureArea from "./server-feature-components/FeatureArea";
import FeatureTitle from "./server-feature-components/FeatureTitle";
import LanguageFeatureArea from "@views/common/server-feature-components/LanguageFeatureArea";

export default {
  provide() {
    return {
      editable: this.editable,
      availableFrameworks: this.availableFrameworks,
    };
  },
  components: {
    LanguageFeatureArea,
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
    this.$store.dispatch("server/features/get");
    this.$store.dispatch("server/languages/get");
    this.$store.dispatch("server/frameworks/get");

    this.$router.push({
      name: "site.server-features",
      params: {
        section: Object.keys(this.availableServerFeatures)[0],
      },
    });
  },
  methods: {
    hasFeatures(features) {
      return Object.keys(features).length;
    },
  },
  computed: {
    availableLanguages() {
      return this.$store.state.server.languages.languages;
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

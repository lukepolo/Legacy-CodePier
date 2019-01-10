<template>
  <form>
    <template v-if="hasLanguageItems">
      <template v-for="(settings, language) in availableLanguageSettings">
        <h3 class="h--label heading">{{ language }} settings</h3>
        <template v-for="setting in settings">
          <language-setting :setting="setting"></language-setting>
        </template>
      </template>
    </template>
    <template v-else>
      <h3>No language settings available, here.</h3>
    </template>
  </form>
</template>

<script>
import LanguageSetting from "./components/LanguageSetting";
export default {
  components: {
    LanguageSetting,
  },
  watch: {
    $route: {
      immediate: true,
      handler: "fetchData",
    },
  },
  methods: {
    async fetchData() {
      await this.$store.dispatch("user/sites/language_settings/getAvailable", {
        site: this.siteId,
      });
      await this.$store.dispatch("user/sites/language_settings/get", {
        site: this.siteId,
      });
    },
    isRunningCommandFor(id) {
      return false;
      // return this.isCommandRunning("App\\Models\\LanguageSetting", id);
    },
  },
  computed: {
    site() {
      return this.$store.getters["user/sites/show"](this.siteId);
    },
    siteId() {
      return this.$route.params.site;
    },
    hasLanguageItems() {
      return Object.keys(this.availableLanguageSettings).length > 0;
    },
    availableLanguageSettings() {
      return this.$store.state.user.sites.language_settings
        .available_language_settings;
    },
  },
};
</script>

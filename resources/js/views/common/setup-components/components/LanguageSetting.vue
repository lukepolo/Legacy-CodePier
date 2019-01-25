<template>
  <base-form v-form="form" :action="runSetting">
    <h3 class="heading">
      {{ setting.name | startCase }} <br />
      <small>{{ setting.description }}</small>
    </h3>

    <template v-for="(defaultValue, param) in setting.params">
      <base-input
        validate
        :name="param"
        :label="uncamelize(param)"
        v-model="form.params[param]"
      ></base-input>
    </template>

    <template slot="buttons">
      <button class="btn btn-primary" :disabled="!form.isValid()">
        <template v-if="Object.keys(setting.params).length">
          Update {{ setting.name | startCase }}
        </template>
        <template v-else>
          Run {{ setting.name | startCase }}
        </template>
      </button>
    </template>
    <!--<template v-if="isCommandCurrentlyRunning">-->
    <!--<div class="flyform&#45;&#45;footer-links">-->
    <!--{{ isCommandCurrentlyRunning.status }}-->
    <!--</div>-->
    <!--</template>-->
  </base-form>
</template>

<script>
import uncamelize from "varie/lib/utilities/uncamelize";
export default {
  props: ["setting"],
  data({ setting }) {
    let validation = {};

    for (let param in setting.params) {
      validation[param] = "required";
      // TODO - we can check to see what the default is , (also can we safely assume always > 0)
    }

    return {
      form: this.createForm({
        params: {},
        setting: setting.name,
        language: setting.type,
      }).validation({
        rules: {
          params: validation,
        },
      }),
    };
  },
  watch: {
    siteLanguageSetting: {
      immediate: true,
      handler() {
        if (this.setting.params) {
          for (let param in this.setting.params) {
            this.$set(
              this.form.params,
              param,
              (this.siteLanguageSetting &&
                this.siteLanguageSetting.params[param]) ||
                this.setting.params[param],
            );
          }
        }
      },
    },
  },
  methods: {
    uncamelize(param) {
      return uncamelize(param);
    },
    runSetting() {
      this.$store.dispatch("user/sites/language_settings/create", {
        data: this.form.data(),
        parameters: {
          site: this.siteId,
        },
      });
    },
  },
  computed: {
    siteId() {
      return this.$route.params.site;
    },
    siteLanguageSetting() {
      return this.languageSettings.find((setting) => {
        return setting.setting === this.setting.name;
      });
    },
    languageSettings() {
      return this.$store.state.user.sites.language_settings.language_settings;
    },
  },
};
</script>

<template>
  <label :class="{ disabled: hasConflicts }">
    <template v-if="editable">
      <input type="checkbox" v-model="enabled" :disabled="hasConflicts" />
      <span class="icon"></span>
    </template>

    {{ feature.name }}

    <br />
    <small> {{ feature.description }} </small>

    <template v-if="feature.parameters">
      <div class="flyform--subform">
        <template v-for="(defaultValue, parameter) in feature.parameters">
          <template v-if="feature.options && feature.options[parameter]">
            <div class="flyform--group">
              <label>{{ parameter }}</label>
              <div class="flyform--group-select">
                <select :name="parameter" v-model="parameters[parameter]">
                  <template v-for="option in feature.options[parameter]">
                    <option :value="option">{{ option }}</option>
                  </template>
                </select>
              </div>
            </div>
          </template>
          <template v-else>
            <div class="flyform--group">
              <div
                :class="{ 'flyform--group-postfix': inputSuffix(parameter) }"
              >
                <base-input
                  :name="parameter"
                  :label="parameter"
                  :type="inputType(parameter)"
                  v-model="parameters[parameter]"
                ></base-input>
                <template v-if="inputSuffix(parameter)">
                  <div class="flyform--group-postfix-label">
                    {{ inputSuffix(parameter) }}
                  </div>
                </template>
              </div>
            </div>
          </template>
        </template>
      </div>
    </template>

    <!-- FOR INSTALLING FEATURES -->
    <!--<template v-if="server && hasFeature(feature)">-->
    <!--<div class="flyform&#45;&#45;group-actions">-->
    <!--<template v-if="isInstalling(feature)">-->
    <!--<small class="text-warning">[ Installing ]</small>-->
    <!--</template>-->
    <!--<template v-else>-->
    <!--<small class="text-success">[ Installed ]</small>-->
    <!--</template>-->
    <!--</div>-->
    <!--</template>-->
    <!--<template v-else-if="server && !hasFeature(feature)">-->
    <!--<div class="flyform&#45;&#45;group-actions">-->
    <!--<template v-if="hasConflicts(feature)">-->
    <!--<small class="text-error">-->
    <!--conflicts with <br />-->
    <!--{{ hasConflicts(feature) }}-->
    <!--</small>-->
    <!--</template>-->
    <!--<template v-else>-->
    <!--<button-->
    <!--class="btn btn-small btn-primary"-->
    <!--:class="{-->
    <!--'btn-disabled':-->
    <!--server.progress < 100 || serverIsInstallingSomething,-->
    <!--}"-->
    <!--@click="installFeature(feature)"-->
    <!--&gt;-->
    <!--<template-->
    <!--v-if="server.progress < 100 || serverIsInstallingSomething"-->
    <!--&gt;-->
    <!--Please Wait-->
    <!--</template>-->
    <!--<template v-else>-->
    <!--Install-->
    <!--</template>-->
    <!--</button>-->
    <!--</template>-->
    <!--</div>-->
    <!--</template>-->
    <!---->
    <!---->
  </label>
</template>

<script>
export default {
  inject: ["editable"],
  model: {
    prop: "selectedServerFeatures",
  },
  props: {
    feature: {
      required: true,
    },
    selectedServerFeatures: {
      required: true,
    },
  },
  data({ feature }) {
    return {
      enabled: false,
      parameters: feature.parameters,
    };
  },
  created() {
    let feature =
      this.selectedServerFeatures[this.service] &&
      this.selectedServerFeatures[this.service][this.inputName];
    if (feature) {
      this.enabled = feature.enabled;
      this.parameters = Object.assign({}, this.parameters, feature.parameters);
    }
  },
  watch: {
    enabled(enabled) {
      this.updateObject();
      this.$set(
        this.selectedServerFeatures[this.service][this.inputName],
        "enabled",
        enabled,
      );
      this.updateServerFeatures();
    },
    parameters: {
      deep: true,
      handler(parameters) {
        this.updateObject();
        this.$set(
          this.selectedServerFeatures[this.service][this.inputName],
          "parameters",
          parameters,
        );
        this.updateServerFeatures();
      },
    },
  },
  methods: {
    updateObject() {
      if (!this.selectedServerFeatures[this.service]) {
        this.$set(this.selectedServerFeatures, this.service, {});
        this.updateServerFeatures();
      }
      if (!this.selectedServerFeatures[this.service][this.inputName]) {
        this.$set(
          this.selectedServerFeatures[this.service],
          this.inputName,
          {},
        );
        this.updateServerFeatures();
      }
    },
    hasFeature(feature) {
      return this.$store.getters["user/sites/servers/features/hasFeature"](
        this.service,
        feature,
      );
    },
    updateServerFeatures() {
      this.$emit("input", this.selectedServerFeatures);
    },
    inputType(parameter) {
      return (
        this.feature.parameter_options &&
        this.feature.parameter_options[parameter] &&
        this.feature.parameter_options[parameter].type
      );
    },
    inputSuffix(parameter) {
      return (
        this.feature.parameter_options &&
        this.feature.parameter_options[parameter] &&
        this.feature.parameter_options[parameter].suffix
      );
    },
  },
  computed: {
    service() {
      return this.feature.service;
    },
    inputName() {
      return this.feature.input_name;
    },
    hasConflicts() {
      if (this.feature.conflicts.length) {
        for (let conflict in this.feature.conflicts) {
          if (this.hasFeature(this.feature.conflicts[conflict])) {
            return true;
          }
        }
      }
      return false;
    },
  },
};
</script>

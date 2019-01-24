<template>
  <div>
    <template v-if="feature.options && feature.options[parameter]">
      <div class="flyform--group">
        <label>{{ parameter }}</label>
        <div class="flyform--group-select">
          <select :name="parameter" v-model="parameters">
            <template v-for="option in feature.options[parameter]">
              <option :value="option">{{ option }}</option>
            </template>
          </select>
        </div>
      </div>
    </template>
    <template v-else>
      <div class="flyform--group">
        <div :class="{ 'flyform--group-postfix': inputSuffix }">
          <base-input
            :name="parameter"
            :label="parameter"
            :type="inputType"
            v-model="parameters"
          ></base-input>
          <template v-if="inputSuffix">
            <div class="flyform--group-postfix-label">{{ inputSuffix }}</div>
          </template>
        </div>
      </div>
    </template>
  </div>
</template>

<script>
export default {
  props: {
    feature: {
      required: true,
    },
    parameter: {
      required: true,
    },
    defaultValue: {
      required: true,
    },
    value: {
      required: true,
    },
  },
  computed: {
    parameters: {
      get() {
        return this.value;
      },
      set(value) {
        this.$emit("input", value);
      },
    },
    inputType() {
      return this.parameterOptions && this.parameterOptions.type;
    },
    inputSuffix() {
      return this.parameterOptions && this.parameterOptions.suffix;
    },
    parameterOptions() {
      return (
        this.feature.parameter_options &&
        this.feature.parameter_options[this.parameter]
      );
    },
  },
};
</script>

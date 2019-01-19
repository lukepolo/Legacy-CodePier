<template>
  <section>
    <template v-for="feature in features">
      <div class="flyform--group-checkbox action-right">
        <label :class="{ disabled: hasConflicts(feature) }">
          <template v-if="!server">
            <input
              v-on:click="updateValue(feature, $event.target.checked)"
              value="1"
              type="checkbox"
              :name="getInputName(feature)"
              :disabled="hasConflicts(feature)"
              :checked="(server && feature.required) || hasFeature(feature)"
              v-if="!server"
            />
            <span class="icon"></span>
          </template>

          {{ feature.name }}

          <br />
          <small> {{ feature.description }} </small>
        </label>

        <template v-if="server && hasFeature(feature)">
          <div class="flyform--group-actions">
            <template v-if="isInstalling(feature)">
              <small class="text-warning">[ Installing ]</small>
            </template>
            <template v-else>
              <small class="text-success">[ Installed ]</small>
            </template>
          </div>
        </template>
        <template v-else-if="server && !hasFeature(feature)">
          <div class="flyform--group-actions">
            <template v-if="hasConflicts(feature)">
              <small class="text-error">
                conflicts with <br />
                {{ hasConflicts(feature) }}
              </small>
            </template>
            <template v-else>
              <button
                class="btn btn-small btn-primary"
                :class="{
                  'btn-disabled':
                    server.progress < 100 || serverIsInstallingSomething,
                }"
                @click="installFeature(feature)"
              >
                <template
                  v-if="server.progress < 100 || serverIsInstallingSomething"
                >
                  Please Wait
                </template>
                <template v-else>
                  Install
                </template>
              </button>
            </template>
          </div>
        </template>
      </div>

      <template
        v-if="isObject(feature.parameters) && (!server || !hasFeature(feature))"
      >
        <div class="flyform--subform">
          <template v-for="(value, parameter) in feature.parameters">
            <template v-if="feature.options">
              <div class="flyform--group">
                <label>{{ parameter }}</label>
                <div class="flyform--group-select">
                  <select :name="getInputName(feature, parameter)">
                    <template v-for="option in feature.options">
                      <option
                        :selected="
                          getParameterValue(feature, parameter, value) ===
                            option
                        "
                        :value="option"
                        >{{ option }}
                      </option>
                    </template>
                  </select>
                </div>
              </div>
            </template>

            <template v-else>
              <div class="flyform--group">
                <div
                  :class="{
                    'flyform--group-postfix': hasSuffix(feature, parameter),
                  }"
                >
                  <input
                    :id="parameter"
                    :name="getInputName(feature, parameter)"
                    :type="getType(feature, parameter)"
                    :value="getParameterValue(feature, parameter, value)"
                    placeholder=" "
                  />
                  <label :for="parameter">
                    <span>{{ parameter }}</span>
                  </label>
                  <template v-if="hasSuffix(feature, parameter)">
                    <div class="flyform--group-postfix-label">
                      {{ hasSuffix(feature, parameter) }}
                    </div>
                  </template>
                </div>
              </div>
            </template>
          </template>
        </div>
      </template>
    </template>

    <template v-if="frameworks">
      <hr />
      <div class="flyform--group">
        <h3>Frameworks Features for {{ area }}</h3>
      </div>
      <feature-area
        :server="server"
        :area="framework"
        :features="features"
        :selected_server_features="selected_server_features"
        v-for="(features, framework) in getFrameworks(area)"
        :key="framework"
      >
      </feature-area>
    </template>
  </section>
</template>

<script>
export default {
  name: "featureArea",
  props: [
    "area",
    "features",
    "frameworks",
    "server",
    "selected_server_features",
    "current_selected_features",
  ],
  methods: {
    updateValue(feature, enabled) {
      this.$emit("featuresChanged", feature, enabled);
    },
    getInputName: function(feature, parameter) {
      let name =
        "services[" + feature.service + "]" + "[" + feature.input_name + "]";

      if (parameter) {
        name = name + "[parameters][" + parameter + "]";

        if (feature.multiple === true) {
          name = name + "[]";
        }

        return name;
      }

      return name + "[enabled]";
    },
    hasFeature: function(feature) {
      let areaFeatures = null;

      if (this.server && this.server.server_features) {
        areaFeatures = _.get(this.server.server_features, feature.service);
      } else if (this.selected_server_features) {
        areaFeatures = _.get(this.selected_server_features, feature.service);
      }

      if (
        areaFeatures &&
        _.has(areaFeatures, feature.input_name) &&
        (areaFeatures[feature.input_name].enabled ||
          areaFeatures[feature.input_name].installing)
      ) {
        return _.get(areaFeatures, feature.input_name);
      }

      return false;
    },
    isInstalling: function(feature) {
      let foundFeature = this.hasFeature(feature);
      if (foundFeature) {
        return foundFeature.installing;
      }
      return false;
    },
    currentlySelectedHasFeature: function(service, feature) {
      let areaFeatures = null;

      if (this.current_selected_features) {
        areaFeatures = _.get(this.current_selected_features, service);
      } else {
        areaFeatures = _.get(this.selected_server_features, service);
      }

      if (_.has(areaFeatures, feature) && areaFeatures[feature].enabled) {
        return feature;
      }

      return false;
    },
    getParameterValue: function(feature, parameter, default_value) {
      let area = this.hasFeature(feature);

      if (area && area.parameters) {
        return area.parameters[parameter];
      }

      return default_value;
    },
    installFeature: function(feature) {
      let parameters = {};

      _.each(feature.parameters, function(value, parameter) {
        parameters[parameter] = $("#" + parameter).val();
      });

      this.$store.dispatch("user_server_features/install", {
        feature: feature.input_name,
        parameters: parameters,
        server: this.server.id,
        service: feature.service,
      });
    },
    getFrameworks: function(area) {
      return this.availableServerFrameworks[area];
    },
    hasConflicts: function(feature) {
      if (feature.conflicts.length) {
        return this.currentlySelectedHasFeature(
          feature.service,
          feature.conflicts[0],
        );
      }
      return false;
    },
    isObject(params) {
      return _.keys(params).length;
    },
    getType(feature, parameter) {
      let options = feature.parameter_options[parameter];
      if (options && options.type) {
        return options.type;
      }
      return "text";
    },
    hasSuffix(feature, parameter) {
      let options = feature.parameter_options[parameter];
      if (options && options.suffix) {
        return options.suffix;
      }
    },
  },
  computed: {
    availableServerFrameworks() {
      return this.$store.state.server_frameworks.frameworks;
    },
    serverIsInstallingSomething() {
      if (this.server) {
        return this.isCommandRunning(
          "App\\Models\\Server\\Server",
          this.server.id,
        );
      }
      return false;
    },
  },
};
</script>

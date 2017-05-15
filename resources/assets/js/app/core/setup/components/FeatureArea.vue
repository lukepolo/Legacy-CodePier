<template>
    <section>
        <template v-for="feature in features">

            <div class="jcf-input-group input-checkbox">

                <label :class="{ disabled : hasConflicts(feature) }">

                    <template v-if="!server">
                        <input
                            v-on:click="updateValue(feature, $event.target.checked)"
                            value="1"
                            type="checkbox"
                            :name="getInputName(feature)"
                            :disabled="hasConflicts(feature)"
                            :checked="(server && feature.required) || hasFeature(feature)"
                            v-if="!server"
                        >
                        <span class="icon"></span>
                    </template>

                    {{ feature.name }}

                    <br>
                    <small>
                        {{ feature.description }}
                    </small>

                    <template v-if="server && hasFeature(feature)">
                        [Installed]
                    </template>
                    <template v-else-if="server && !hasFeature(feature)">
                        <template v-if="hasConflicts(feature)">
                            <p>
                                conflicts with {{ hasConflicts(feature) }}
                            </p>
                        </template>
                        <template v-else>
                            <div class="btn btn-small btn-primary" @click="installFeature(feature)">Install</div>
                        </template>
                    </template>

                </label>

            </div>

            <template v-if="isObject(feature.parameters)">
                <div class="jcf-sub-form">
                    <template v-for="(value, parameter) in feature.parameters">
                        <template v-if="feature.options">
                            <div class="jcf-input-group">
                                <div class="input-question">{{ parameter }}</div>
                                <div class="select-wrap">
                                    <select :name="getInputName(feature, parameter)">
                                        <template v-for="option in feature.options">
                                            <option :selected="getParameterValue(feature, parameter, value) == option" :value="option">{{ option }}</option>
                                        </template>
                                    </select>
                                </div>
                            </div>
                        </template>

                        <template v-else>
                            <div class="jcf-input-group">
                                <input
                                        :id="parameter"
                                        :name="getInputName(feature, parameter)"
                                        type="text" :value="getParameterValue(feature, parameter, value)"
                                >
                                <label :for="parameter">
                                    <span class="float-label">{{ parameter }}</span>
                                </label>
                            </div>
                        </template>
                    </template>
                </div>
            </template>

            <!--<template v-if="server && hasFeature(feature)">-->
                <!--Im not sure if we are able to update or not-->
                <!--<button class="btn btn-primary" @click="installFeature(feature)">Update</button>-->
            <!--</template>-->

        </template>

        <template v-if="frameworks">
            <hr>
            <div class="jcf-input-group">
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
        name: 'featureArea',
        props: ['area', 'features', 'frameworks', 'server', 'selected_server_features', 'current_selected_features'],
        methods: {
            updateValue(feature, enabled) {
                this.$emit('featuresChanged', feature, enabled)
            },
            getInputName : function(feature, parameter) {

                let name = 'services[' + feature.service + ']' + '[' + feature.input_name + ']';

                if(parameter) {
                    name = name + '[parameters][' + parameter + ']';

                    if(feature.multiple == true) {
                        name = name + '[]';
                    }

                    return name;
                }

                return name + '[enabled]';
            },
            hasFeature: function (feature) {

                let areaFeatures = null;

                if (this.server && this.server.server_features) {
                    areaFeatures = _.get(this.server.server_features, feature.service);
                } else if (this.selected_server_features) {
                    areaFeatures = _.get(this.selected_server_features, feature.service);
                }

                if(areaFeatures && _.has(areaFeatures, feature.input_name) && areaFeatures[feature.input_name].enabled) {
                    return _.get(areaFeatures, feature.input_name);
                }

                return false;
            },
            currentlySelectedHasFeature: function (service, feature) {

                let areaFeatures = null;

                if(this.current_selected_features) {
                    areaFeatures = _.get(this.current_selected_features, service);
                } else {
                    areaFeatures = _.get(this.selected_server_features, service);
                }

                if(_.has(areaFeatures, feature) && areaFeatures[feature].enabled) {
                    return feature;
                }

                return false;
            },
            getParameterValue: function (feature, parameter, default_value) {
                let area = this.hasFeature(feature);

                if (area && area.parameters) {
                    return area.parameters[parameter];
                }

                return default_value;
            },
            installFeature: function (feature) {
                let parameters = {};

                _.each(feature.parameters, function (value, parameter) {
                    parameters[parameter] = $('#' + parameter).val();
                });

                this.$store.dispatch('installFeature', {
                    feature: feature.input_name,
                    parameters: parameters,
                    server: this.server.id,
                    service: feature.service,
                });
            },
            getFrameworks: function (area) {
                return this.availableServerFrameworks[area];
            },
            hasConflicts: function(feature) {
                if(feature.conflicts.length) {
                    return this.currentlySelectedHasFeature(feature.service, feature.conflicts[0]);
                }
                return false
            },
            isObject(params) {
                return _.keys(params).length
            }
        },
        computed: {
            availableServerFrameworks() {
                return this.$store.state.system_frameworks.frameworks;
            }
        }
    }
</script>

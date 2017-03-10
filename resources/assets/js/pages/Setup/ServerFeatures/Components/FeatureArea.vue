<template>
    <section>
        <template v-for="feature in features">

            <div class="jcf-input-group input-checkbox">

                <label>

                    <template v-if="!server">
                        <input
                            :name="getInputName(feature)"
                            type="checkbox"
                            :checked="(server && feature.required) || hasFeature(feature)"
                            value="1"
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
                        <div class="btn btn-small btn-primary" @click="installFeature(feature)">Install</div>
                    </template>

                </label>

            </div>

            <template v-if="feature.parameters" v-for="(value, parameter) in feature.parameters">

                    <template v-if="feature.options">
                        <div class="input-question">{{ parameter }}</div>
                        <div class="select-wrap">
                            <select :name="getInputName(feature, parameter)">
                                <template v-for="option in feature.options">
                                    <option :selected="getParameterValue(feature, parameter, value) == option" :value="option">{{ option }}</option>
                                </template>
                            </select>
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

            <template v-if="server && hasFeature(feature)">
                <!--Im not sure if we are able to update or not-->
                <!--<button class="btn btn-primary" @click="installFeature(feature)">Update</button>-->
            </template>

        </template>

        <template v-if="frameworks">
            <h2>Frameworks Features for {{ area }}</h2>
            <feature-area
                :server="server"
                :area="framework"
                :features="features"
                :selected_server_features="selected_server_features"
                v-for="(features, framework) in getFrameworks(area)"
            >
            </feature-area>
        </template>
    </section>
</template>

<script>
    export default {
        name: 'featureArea',
        props: ['area', 'features', 'frameworks', 'server', 'selected_server_features'],
        methods: {
            getInputName : function(feature, parameter) {

                let name = 'services[' + feature.service + ']' + '[' + feature.name + ']';

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

                if(areaFeatures && _.has(areaFeatures, feature.name) && areaFeatures[feature.name].enabled) {
                    return _.get(areaFeatures, feature.name);
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
                    feature: feature.name,
                    parameters: parameters,
                    server: this.server.id,
                    service: feature.service,
                });
            },
            getFrameworks: function (area) {
                return this.availableServerFrameworks[area];
            }
        },
        computed: {
            availableServerFrameworks() {
                return this.$store.state.serversStore.available_server_frameworks;
            }
        }
    }
</script>

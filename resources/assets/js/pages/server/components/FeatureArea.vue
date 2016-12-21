<template>
    <section>
        <h3>{{ getSectionTitle(area) }}</h3>
        <template v-for="feature in features">
            <div class="jcf-input-group input-checkbox">
                <div class="input-question">
                    {{ feature.name }}
                </div>
            </div>
            <p>
                <template v-if="server && hasFeature(feature)">
                    Installed
                </template>

                <template v-else>

                    <template v-if="server">
                        <button @click="installFeature(feature)">Install</button>
                    </template>

                    <template v-else>
                        <input
                            :name="getInputName(feature)"
                            type="checkbox"
                            :checked="(server && feature.required) || hasFeature(feature)"
                            value="1"
                        >
                    </template>
                    <div>
                        <small>{{ feature.description }}</small>
                    </div>
                </template>
            </p>

            <template v-if="feature.parameters" v-for="(value, parameter) in feature.parameters">
                <div class="input-group">
                    <template v-if="feature.options">
                        <div class="input-question">{{ parameter }}</div>
                        <div class="select-wrap">
                            <select :name="getInputName(feature, parameter)">
                                <option v-for="option in feature.options">{{ option }}</option>
                            </select>
                        </div>
                    </template>
                    <template v-else>
                        <input
                            :id="parameter"
                            :name="getInputName(feature, parameter)"
                            type="text" :value="getParameterValue(feature, parameter, value)"
                        >
                        <label :for="parameter"><span class="float-label">{{ parameter }}</span></label>
                    </template>
                </div>
            </template>

            <template v-if="server && hasFeature(feature)">
                <button @click="installFeature(feature)">Update</button>
            </template>
        </template>
        <template v-if="frameworks">
            <h2>Frameworks for {{ area }}</h2>
            <feature-area
                :server="server"
                :area="framework"
                :features="features"
                v-for="(features, framework) in getFrameworks(area)"
            >
            </feature-area>
        </template>
    </section>
</template>

<script>
    export default {
        name: 'featureArea',
        props: ['selectable', 'area', 'features', 'frameworks', 'server', 'site_server_features'],
        methods: {
            getInputName : function(feature, parameter) {
                let name = 'services[' + feature.service + ']' + '[' + feature.name + ']';

                if(parameter) {
                    name = name + '[parameters][' + parameter + ']';

                    if(feature.multiple) {
                        name = name + '[]';
                    }

                    return name;
                }

                return name + '[enabled]';
            },
            hasFeature: function (feature) {
                let areaFeatures = null;

                if (this.server && this.server.server_features) {
                    areaFeatures = _.get(this.server.server_features, this.area);
                } else if (this.site_server_features && this.site_server_features) {
                    areaFeatures = _.get(this.site_server_features, this.area);
                }

                if(areaFeatures && _.has(areaFeatures, feature.name) && areaFeatures[feature.name].enabled) {
                    return feature;
                }

                return false;
            },
            getParameterValue: function (feature, parameter, default_value) {

                let area = this.hasFeature(feature);

                if (area.parameters) {
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
            getSectionTitle: function (area) {
                return area;
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

<template>
    <section>
        <h4>{{ getSectionTitle(area) }}</h4>
        <template v-for="feature in features">
            <p>
                {{ feature.name }}
                <template v-if="hasFeature(feature)">
                    Installed
                </template>
                <template v-else>
                    <template v-if="server">
                        <button @click="installFeature(feature)">Install</button>
                    </template>
                    <template v-else>
                        <input :name="'services[' + area + ']['+feature.name + '][enabled]'" type="checkbox" :checked="feature.required" value="1">
                    </template>
                    <p>
                        <small>{{ feature.description }}</small>
                    </p>
                </template>
            </p>
            <template v-if="!hasFeature(feature) && feature.parameters"
                      v-for="(value, parameter) in feature.parameters">
                <div class="input-group">
                    <input :id="parameter" :name="'services[' + area + ']' + '[' + feature.name + '][parameters]['+ parameter+']'" type="text" :value="value">
                    <label :for="parameter"><span class="float-label">{{ parameter }}</span></label>
                </div>
            </template>
        </template>
        <template v-if="frameworks">
            <h2>Frameworks for {{ area }}</h2>
            <feature-area :server="server" :area="framework" :features="features" v-for="(features, framework) in getFrameworks(area)"></feature-area>
        </template>
    </section>
</template>

<script>
    export default {
        name: 'featureArea',
        props: ['selectable', 'area', 'features', 'frameworks', 'server'],
        methods: {
            hasFeature: function (feature) {
                if (this.server) {
                    var areaFeatures = this.server.server_features[feature.service];
                    if (areaFeatures && areaFeatures[feature.name] && areaFeatures[feature.name].enabled) {
                        return true;
                    }
                }

                return false;
            },
            installFeature: function (feature) {
                var parameters = {};

                _.each(feature.parameters, function (value, parameter) {
                    parameters[parameter] = $('#' + parameter).val();
                });

                serverStore.dispatch('installFeature', {
                    feature: feature.name,
                    parameters: parameters,
                    server: this.server.id,
                    service: feature.service,
                });
            },
            getSectionTitle : function(area) {
                return area;
            },
            getFrameworks : function(area) {
                return this.availableServerFrameworks[area];
            }
        },
        computed: {
            availableServerFrameworks: () => {
                return serverStore.state.available_server_frameworks;
            }
        }
    }
</script>

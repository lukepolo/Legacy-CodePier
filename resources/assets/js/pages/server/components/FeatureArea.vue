<template>
    <section>
        <h4>{{ area }}</h4>
        <template v-for="feature in features">
            <div class="input-group input-checkbox">
                <label>
                    <template v-if="hasFeature(feature.name)">
                        Installed
                    </template>
                    <template v-else>
                        <input :name="'services[' + area + ']['+feature.name + '][enabled]'" type="checkbox"
                               :checked="feature.required" value="1">
                        <template v-if="server">
                            <button @click="installFeature(feature)">Install</button>
                        </template>
                    </template>

                    <span class="icon"></span>{{ feature.name }}
                </label>
            </div>
            <template v-if="!hasFeature(feature.name) && feature.parameters"
                      v-for="(value, parameter) in feature.parameters">
                <div class="input-group">
                    <input :id="parameter"
                           :name="'services[' + area + ']' + '[' + feature.name + '][parameters]['+ parameter+']'"
                           type="text" :value="value">
                    <label :for="parameter"><span class="float-label">{{ parameter }}</span></label>
                </div>
            </template>
        </template>
        <template v-if="frameworks">
            <h2>Frameworks for {{ area }}</h2>
            <feature-area :sever="server" :area="language" :features="features"
                          v-for="(features, language) in availableServerFrameworks[area]"></feature-area>
        </template>
    </section>
</template>

<script>
    export default {
        name: 'featureArea',
        props: ['area', 'features', 'frameworks', 'server'],
        methods: {
            hasFeature: function (feature) {
                if (this.server) {
                    var areaFeatures = this.server.server_features[this.area];

                    if (areaFeatures && areaFeatures[feature] && areaFeatures[feature].enabled) {
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
                    server: this.server.id,
                    area: this.area,
                    feature: feature.name,
                    parameters: parameters
                });
            }
        },
        computed: {
            availableServerFrameworks: () => {
                return serverStore.state.available_server_frameworks;
            }
        }
    }
</script>

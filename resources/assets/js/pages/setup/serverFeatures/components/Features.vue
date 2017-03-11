<template>
    <section>
        <div class="tab-container tab-left">

            <ul class="nav nav-tabs">

                <template v-for="(features, serverFeatureArea) in availableServerFeatures">
                    <li :class="{ 'router-link-active' : section == serverFeatureArea }" v-if="features.length != 0">
                        <a @click="switchSection(serverFeatureArea)">
                            {{ getSectionTitle(serverFeatureArea) }}
                        </a>
                    </li>
                </template>

                <template v-for="(features, serverLanguageArea) in availableServerLanguages">
                    <li :class="{ 'router-link-active' : section == serverLanguageArea }" v-if="features.length != 0">
                        <a @click="switchSection(serverLanguageArea)">
                            {{ getSectionTitle(serverLanguageArea) }}
                        </a>
                    </li>
                </template>

            </ul>

            <div class="tab-content">

                <div role="tabpanel" class="tab-pane active">

                    <feature-area
                            :server="server"
                            :selected_server_features="serverFeatures"
                            :area="serverFeatureArea"
                            :features="features"
                            v-for="(features, serverFeatureArea) in availableServerFeatures"
                            v-show="section == serverFeatureArea"
                    ></feature-area>
                    <feature-area
                            :server="server"
                            :selected_server_features="serverFeatures"
                            :area="serverLanguageArea"
                            :features="features"
                            :frameworks="true"
                            v-for="(features, serverLanguageArea) in availableServerLanguages"
                            v-show="section == serverLanguageArea"
                    ></feature-area>

                </div>

            </div>

        </div>
    </section>
</template>

<script>
    import FeatureArea from './FeatureArea.vue'

    export default {
        components: {
            FeatureArea
        },
        created() {
            this.fetchData();
        },
        watch: {
            '$route': 'fetchData'
        },
        data() {
            return {
                section : null
            }
        },
        methods: {
            fetchData() {

                if(this.siteId) {
                    this.$store.dispatch('getSiteServerFeatures', this.siteId);
                }

                if(this.serverId) {
                    this.$store.dispatch('getServerFeatures', this.serverId);
                }

                this.$store.dispatch('getServerAvailableFeatures');
                this.$store.dispatch('getServerAvailableLanguages');
                this.$store.dispatch('getServerAvailableFrameworks');
            },
            getSectionTitle: function (area) {
                let areaName = area;
                if((/[a-z]/.test(area))) {
                    areaName = area.replace(/([A-Z].*)(?=[A-Z]).*/g, '$1')
                }
                return areaName + ' Features';
            },
            switchSection: function(area) {
                Vue.set(this, 'section', area)
            }
        },
        computed: {
            siteId() {
                return this.$route.params.site_id
            },
            server() {
                if(this.serverId) {
                    return this.$store.state.serversStore.server;
                }
            },
            serverId() {
                console.info(this.$route.params)
                return this.$route.params.server_id
            },
            serverFeatures() {
                if(this.siteId) {
                    return this.$store.state.siteServersFeaturesStore.site_server_features;
                }
                if(this.serverId) {
                    return this.$store.state.serversStore.server_installed_features;
                }
            },
            availableServerFeatures() {
                let serverFeatures = this.$store.state.serversStore.available_server_features

                this.section = _.keys(serverFeatures)[0]

                return serverFeatures
            },
            availableServerLanguages() {
                return this.$store.state.serversStore.available_server_languages;
            },
            availableServerFrameworks() {
                return this.$store.state.serversStore.available_server_frameworks;
            },
        }
    }
</script>

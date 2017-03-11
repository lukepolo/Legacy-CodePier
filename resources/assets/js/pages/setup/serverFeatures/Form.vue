<template>
    <section>
        <div class="jcf-form-wrap">
            <form @submit.prevent="dispatchMethod" enctype="multipart/form-data" class="floating-labels">

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

                <div class="btn-footer" v-if="!server && update != false">
                    <button class="btn btn-primary" type="submit">Update Site Server Features</button>
                </div>

            </form>
        </div>

    </section>
</template>

<script>
    import FeatureArea from './components/FeatureArea.vue'

    export default {
        props : ["dispatch", "update"],
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
                if(this.$route.params.server_id) {
                    this.$store.dispatch('getServerFeatures', this.$route.params.server_id);
                }

                if(this.$route.params.site_id) {
                    this.$store.dispatch('getSiteServerFeatures', this.$route.params.site_id);
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
            },
            dispatchMethod() {
                this.$store.dispatch(this.dispatch, _.merge(
                    this.$route.params, {
                        site: this.site.id,
                        form: this.getFormData(this.$el)
                    })
                );
            }
        },
        computed: {
            site() {
                if(this.$route.params.site_id) {
                    return this.$store.state.sitesStore.site;
                }
            },
            server() {
                if(this.$route.params.server_id) {
                    return this.$store.state.serversStore.server;
                }
            },
            serverFeatures() {
                if(this.$route.params.site_id) {
                    return this.$store.state.siteServersFeaturesStore.site_server_features;
                }
                if(this.$route.params.server_id) {
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
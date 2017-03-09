<template>
    <div v-if="site">
        <div class="jcf-form-wrap">
            <form @submit.prevent="saveSiteServerFeatures" enctype="multipart/form-data" class="floating-labels">

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
                                    :selected_server_features="siteFeatures"
                                    :area="serverFeatureArea"
                                    :features="features"
                                    v-for="(features, serverFeatureArea) in availableServerFeatures"
                                    v-show="section == serverFeatureArea"
                            ></feature-area>

                            <feature-area
                                    :selected_server_features="siteFeatures"
                                    :area="serverLanguageArea"
                                    :features="features"
                                    :frameworks="true"
                                    v-for="(features, serverLanguageArea) in availableServerLanguages"
                                    v-show="section == serverLanguageArea"
                            ></feature-area>

                        </div>

                    </div>

                </div>



                <div class="btn-footer">
                    <button class="btn btn-primary" type="submit">Update Site Server Features</button>
                </div>

            </form>
        </div>
    </div>
</template>

<script>
    import FeatureArea from './../server/components/FeatureArea.vue'

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
                this.$store.dispatch('getSiteServerFeatures', this.$route.params.site_id);
                this.$store.dispatch('getServerAvailableFeatures');
                this.$store.dispatch('getServerAvailableLanguages');
                this.$store.dispatch('getServerAvailableFrameworks');
            },
            saveSiteServerFeatures() {
                this.$store.dispatch('updateSiteServerFeatures', {
                    site: this.site.id,
                    form: this.getFormData(this.$el)
                });
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
            site() {
                return this.$store.state.sitesStore.site;
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
            siteFeatures() {
                return this.$store.state.siteServersFeaturesStore.site_server_features;
            }
        }
    }
</script>
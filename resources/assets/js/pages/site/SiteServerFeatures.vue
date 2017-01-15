<template>
    <div v-if="site">
        <div class="jcf-form-wrap">
            <form @submit.prevent="saveSiteServerFeatures" enctype="multipart/form-data">
                    <feature-area
                        :selected_server_features="siteFeatures"
                        :area="serverFeatureArea"
                        :features="features"
                        v-for="(features, serverFeatureArea) in availableServerFeatures"
                    ></feature-area>
                    <feature-area
                        :selected_server_features="siteFeatures"
                        :area="serverLanguageArea"
                        :features="features"
                        :frameworks="true"
                        v-for="(features, serverLanguageArea) in availableServerLanguages"
                    ></feature-area>
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
        methods: {
            fetchData() {
                this.$store.dispatch('getSite', this.$route.params.site_id);
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
            }
        },
        computed: {
            site() {
                return this.$store.state.sitesStore.site;
            },
            availableServerFeatures() {
                return this.$store.state.serversStore.available_server_features;
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
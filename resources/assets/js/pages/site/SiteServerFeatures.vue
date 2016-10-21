<template>
    <section>
        <div v-if="site">
            <form @submit.prevent="saveSiteServerFeatures" enctype="multipart/form-data">
                <section>
                    <feature-area :site="site" selectable="true" :area="serverFeatureArea" :features="features"
                                  v-for="(features, serverFeatureArea) in availableServerFeatures"></feature-area>
                    <feature-area :site="site" selectable="true" :area="serverLanguageArea" :features="features"
                                  :frameworks="true"
                                  v-for="(features, serverLanguageArea) in availableServerLanguages"></feature-area>
                </section>
                <button type="submit">Update Site Server Features</button>
            </form>
        </div>
    </section>
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
            }
        }
    }
</script>
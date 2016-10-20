<template>
    <section>
        <left-nav></left-nav>
        <section id="middle" class="section-column">
            <site-header></site-header>
            <div class="section-content" v-if="site">
                <div class="container">
                    <site-nav></site-nav>

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
            </div>
        </section>
        <servers></servers>
    </section>
</template>

<script>
    import LeftNav from './../../core/LeftNav.vue';
    import SiteNav from './components/SiteNav.vue';
    import Servers from './components/Servers.vue';
    import SiteHeader from './components/SiteHeader.vue';
    import SiteFile from './../../components/SiteFile.vue';
    import FeatureArea from './../server/components/FeatureArea.vue'

    export default {
        components: {
            SiteNav,
            LeftNav,
            Servers,
            SiteFile,
            SiteHeader,
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
            site: () => {
                return siteStore.state.site;
            },
            availableServerFeatures: () => {
                return this.$store.state.serversStoreavailable_server_features;
            },
            availableServerLanguages: () => {
                return this.$store.state.serversStoreavailable_server_languages;
            },
            availableServerFrameworks: () => {
                return this.$store.state.serversStoreavailable_server_frameworks;
            }
        }
    }
</script>
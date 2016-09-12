<template>
    <section>
        <left-nav></left-nav>
        <section id="middle" class="section-column">
            <site-header></site-header>
            <div class="section-content" v-if="site">
                <div class="container">
                    <site-nav></site-nav>

                    <form @submit.prevent="saveSiteServerFeatures">
                        <section>
                            <feature-area selectable="true" :area="serverFeatureArea" :features="features" v-for="(features, serverFeatureArea) in availableServerFeatures"></feature-area>
                            <feature-area selectable="true" :area="serverLanguageArea" :features="features" :frameworks="true" v-for="(features, serverLanguageArea) in availableServerLanguages"></feature-area>
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
            fetchData: function() {
                siteStore.dispatch('getSite', this.$route.params.site_id);
                serverStore.dispatch('getServerAvailableFeatures');
                serverStore.dispatch('getServerAvailableLanguages');
                serverStore.dispatch('getServerAvailableFrameworks');
            },
            saveSiteServerFeatures : function() {
                siteStore.dispatch('updateSiteServerFeatures', {
                    site : this.site.id,
                    data : this.getFormData($(this.$el))
                });
            }
        },
        computed: {
            site: () => {
                return siteStore.state.site;
            },
            availableServerFeatures: () => {
                return serverStore.state.available_server_features;
            },
            availableServerLanguages: () => {
                return serverStore.state.available_server_languages;
            },
            availableServerFrameworks: () => {
                return serverStore.state.available_server_frameworks;
            }
        }
    }
</script>
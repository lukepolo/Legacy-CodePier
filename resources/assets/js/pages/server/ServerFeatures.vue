<template>
    <section>
        <left-nav></left-nav>
        <section id="middle" class="section-column">
            <h3 class="section-header primary">Server Features</h3>
            <feature-area :server="server" :area="serverFeatureArea" :features="features" v-for="(features, serverFeatureArea) in availableServerFeatures"></feature-area>
            <feature-area :server="server" :area="serverLanguageArea" :features="features" :frameworks="true" v-for="(features, serverLanguageArea) in availableServerLanguages"></feature-area>
        </section>
    </section>
</template>

<script>
    import LeftNav from './../../core/LeftNav.vue';
    import FeatureArea from './components/FeatureArea.vue'
    export default {
        components: {
            LeftNav,
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
                serverStore.dispatch('getServer', this.$route.params.server_id);
                serverStore.dispatch('getServerAvailableFeatures');
                serverStore.dispatch('getServerAvailableLanguages');
                serverStore.dispatch('getServerAvailableFrameworks');
            }
        },
        computed: {
            availableServerFeatures: () => {
                return serverStore.state.available_server_features;
            },
            availableServerLanguages: () => {
                return serverStore.state.available_server_languages;
            },
            availableServerFrameworks: () => {
                return serverStore.state.available_server_frameworks;
            },
            server : () => {
                return serverStore.state.server;
            },
        }
    }
</script>
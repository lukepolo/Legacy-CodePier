<template>
    <section>
        <section id="middle" class="section-column">
            <feature-area
                :server="server"
                :selected_server_features="serverFeatures"
                :area="serverFeatureArea"
                :features="features"
                v-for="(features, serverFeatureArea) in availableServerFeatures"
            ></feature-area>
            <feature-area
                :server="server"
                :selected_server_features="serverFeatures"
                :area="serverLanguageArea"
                :features="features"
                :frameworks="true"
                v-for="(features, serverLanguageArea) in availableServerLanguages"
            ></feature-area>
        </section>
    </section>
</template>

<script>
    import FeatureArea from './components/FeatureArea.vue'

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
                this.$store.dispatch('getServerFeatures', this.$route.params.server_id);
                this.$store.dispatch('getServerAvailableFeatures');
                this.$store.dispatch('getServerAvailableLanguages');
                this.$store.dispatch('getServerAvailableFrameworks');
            }
        },
        computed: {
            availableServerFeatures() {
                return this.$store.state.serversStore.available_server_features;
            },
            availableServerLanguages() {
                return this.$store.state.serversStore.available_server_languages;
            },
            availableServerFrameworks() {
                return this.$store.state.serversStore.available_server_frameworks;
            },
            server() {
                return this.$store.state.serversStore.server;
            },
            serverFeatures() {
                return this.$store.state.serversStore.server_installed_features;
            }
        }
    }
</script>
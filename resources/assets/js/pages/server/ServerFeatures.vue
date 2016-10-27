<template>
    <section>
        <left-nav></left-nav>
        <section id="middle" class="section-column">
            <server-nav :server="server"></server-nav>
            <feature-area :server="server" :area="serverFeatureArea" :features="features"
                          v-for="(features, serverFeatureArea) in availableServerFeatures"></feature-area>
            <feature-area :server="server" :area="serverLanguageArea" :features="features" :frameworks="true"
                          v-for="(features, serverLanguageArea) in availableServerLanguages"></feature-area>
        </section>
    </section>
</template>

<script>
    import LeftNav from './../../core/LeftNav.vue';
    import ServerNav from './components/ServerNav.vue';
    import FeatureArea from './components/FeatureArea.vue'
    import EditableServerFiles from './components/EditableServerFiles.vue'

    export default {
        components: {
            LeftNav,
            ServerNav,
            FeatureArea,
            EditableServerFiles
        },
        created() {
            this.fetchData();
        },
        watch: {
            '$route': 'fetchData'
        },
        methods: {
            fetchData() {
                this.$store.dispatch('getServer', this.$route.params.server_id);
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
            }
        }
    }
</script>
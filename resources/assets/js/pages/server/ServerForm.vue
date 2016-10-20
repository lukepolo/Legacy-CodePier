<template>
    <section>
        <left-nav></left-nav>
        <section id="middle" class="section-column">
            <h3 class="section-header primary">Create New Server</h3>

            <div class="section-content">
                <div class="container">
                    <div class="jcf-form-wrap">
                        <form @submit.prevent="createServer()" class="validation-form floating-labels">
                            <template v-if="site">
                                <input type="hidden" name="site" :value="site.id">
                            </template>
                            <template v-else>
                                <input type="hidden" name="pile_id" :value="pile.id">
                            </template>

                            <div class="input-group input-radio">
                                <div class="input-question">Server Provider</div>
                                <template v-for="user_server_provider in user_server_providers">
                                    <label>
                                        <input @change="getProviderData(user_server_provider.server_provider.provider_name)"
                                               type="radio" name="server_provider_id"
                                               :value="user_server_provider.server_provider_id">
                                        <span class="icon"></span>
                                        {{ user_server_provider.server_provider.name }}
                                    </label>
                                </template>
                            </div>
                            <template
                                    v-if="server_options.length && server_regions.length && server_provider_features.length">
                                <div class="input-group">
                                    <input type="text" id="server_name" name="server_name" required>
                                    <label for="server_name"><span class="float-label">Name</span></label>
                                </div>

                                <div class="input-group">
                                    <div class="input-question">Server Option</div>

                                    <select name="server_option">
                                        <option v-for="option in server_options" :value="option.id">{{ option.memory }}
                                            MB
                                            RAM - {{ option.cpus }} CPUS - {{ option.space }} SSD - ${{
                                            option.priceHourly }} / Hour - ${{ option.priceMonthly }} / Month
                                        </option>
                                    </select>
                                </div>

                                <div class="input-group">
                                    <div class="input-question">Server Region</div>

                                    <select name="server_region">
                                        <option v-for="region in server_regions" :value="region.id">{{ region.name }}
                                        </option>
                                    </select>
                                </div>

                                <div class="input-group input-checkbox">
                                    <div class="input-question">Server Options</div>
                                    <template v-for="feature in server_provider_features">
                                        <label>
                                            <input type="checkbox" name="server_provider_features[]"
                                                   :value="feature.id">
                                            <span class="icon"></span>{{ 'Enable ' + feature.feature }}
                                            <small>{{ feature.cost }}</small>
                                        </label>
                                    </template>
                                </div>

                                <feature-area :site="site" :area="serverFeatureArea" :features="features"
                                              v-for="(features, serverFeatureArea) in availableServerFeatures"></feature-area>
                                <feature-area :site="site" :area="serverLanguageArea" :features="features" :frameworks="true"
                                              v-for="(features, serverLanguageArea) in availableServerLanguages"></feature-area>

                                <div class="btn-footer">
                                    <button class="btn">Cancel</button>
                                    <button type="submit" class="btn btn-primary">Create Server</button>
                                </div>

                            </template>
                            <template v-else>
                                Please select a provider.
                            </template>
                        </form>
                    </div>
                </div>
            </div>
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
            fetchData: function () {
                this.$store.dispatch('getUserServerProviders');

                this.$store.dispatch('getServerAvailableFeatures');
                this.$store.dispatch('getServerAvailableLanguages');
                this.$store.dispatch('getServerAvailableFrameworks');

                if (this.$route.query.site) {
                    this.$store.dispatch('getSite', this.$route.query.site);
                }
            },
            getProviderData: (provider) => {
                this.$store.dispatch('getServerProviderOptions', provider);
                this.$store.dispatch('getServerProviderRegions', provider);
                this.$store.dispatch('getServerProviderFeatures', provider);
            },
            createServer: function () {
                this.$store.dispatch('createServer', this.getFormData(this.$el));
            }
        },
        computed: {
            user_server_providers: () => {
                var providers = serverProviderStore.state.user_server_providers;
                if (providers.length == 1) {
                }
                return providers;
            },
            server_options: () => {
                return serverProviderStore.state.server_provider_options;
            },
            server_regions: () => {
                return serverProviderStore.state.server_provider_regions;
            },
            server_provider_features: () => {
                return serverProviderStore.state.server_provider_features;
            },
            availableServerFeatures: () => {
                return this.$store.state.serversStoreavailable_server_features;
            },
            availableServerLanguages: () => {
                return this.$store.state.serversStoreavailable_server_languages;
            },
            availableServerFrameworks: () => {
                return this.$store.state.serversStoreavailable_server_frameworks;
            },
            site : () => {
                return siteStore.state.site;
            },
            pile: function () {
                var pile = _.find(user.piles, function (pile) {
                    return pile.id == localStorage.getItem('current_pile_id');
                });

                if (pile) {
                    return pile;
                }

                return {
                    id: null
                }
            }
        }
    }
</script>
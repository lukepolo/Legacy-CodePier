<template>
    <section>
        <left-nav></left-nav>
        <section id="middle" class="section-column">
            <h3 class="section-header primary">Create New Server</h3>

            <div class="section-content">
                <div class="container">
                    <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation" class="active">
                            <a href="#basic" aria-controls="home" role="tab" data-toggle="tab">Basic Server</a>
                        </li>
                        <li role="presentation">
                            <a href="#load-balancer" aria-controls="profile" role="tab" data-toggle="tab">
                                Load Balancer</a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane active" id="basic">
                            <div class="jcf-form-wrap">
                                <form @submit.prevent="createServer()" class="validation-form floating-labels">
                                    <input type="hidden" name="pile_id" :value="pile.id">
                                    <div class="input-group input-radio">
                                        <div class="input-question">Server Provider</div>
                                        <template v-for="user_server_provider in user_server_providers">
                                            <label>
                                                <input @change="getProviderData(user_server_provider.server_provider.provider_name)"
                                                       type="radio" name="server_provider_id"
                                                       :value="user_server_provider.server_provider_id" v-model="form.server_provider_id">
                                                <span class="icon"></span>
                                                {{ user_server_provider.server_provider.name }}
                                            </label>
                                        </template>
                                    </div>
                                    <template v-if="server_options.length && server_regions.length && server_provider_features.length">
                                        <div class="input-group">
                                            <input type="text" id="server_name" name="server_name" required v-model="form.server_name">
                                            <label for="server_name"><span class="float-label">Name</span></label>
                                        </div>

                                        <div class="input-group">
                                            <div class="input-question">Server Option</div>

                                            <select name="server_option" v-model="form.server_option">
                                                <option v-for="option in server_options" :value="option.id">{{ option.memory }}
                                                    MB
                                                    RAM - {{ option.cpus }} CPUS - {{ option.space }} SSD - ${{
                                                    option.priceHourly }} / Hour - ${{ option.priceMonthly }} / Month
                                                </option>
                                            </select>
                                        </div>

                                        <div class="input-group">
                                            <div class="input-question">Server Region</div>

                                            <select name="server_region" v-model="form.server_region">
                                                <option v-for="region in server_regions" :value="region.id">{{ region.name }}
                                                </option>
                                            </select>
                                        </div>

                                        <div class="input-group input-checkbox">
                                            <div class="input-question">Server Options</div>
                                            <template v-for="feature in server_provider_features">
                                                <label>
                                                    <input type="checkbox" name="server_provider_features[]" :value="feature.id" v-model="form.server_provider_features">
                                                    <span class="icon"></span>{{ 'Enable ' + feature.feature }}
                                                    <small>{{ feature.cost }}</small>
                                                </label>
                                            </template>
                                        </div>

                                        <feature-area :area="serverFeatureArea" :features="features" v-for="(features, serverFeatureArea) in availableServerFeatures"></feature-area>
                                        <feature-area :area="serverLanguageArea" :features="features" :frameworks="true" v-for="(features, serverLanguageArea) in availableServerLanguages"></feature-area>

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
                        <div role="tabpanel" class="tab-pane" id="load-balancer">...</div>
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
            fetchData: () => {
                serverProviderStore.dispatch('getUserServerProviders');

                serverStore.dispatch('getServerAvailableFeatures');
                serverStore.dispatch('getServerAvailableLanguages');
                serverStore.dispatch('getServerAvailableFrameworks');
            },
            getProviderData: (provider) => {
                serverProviderStore.dispatch('getServerProviderOptions', provider);
                serverProviderStore.dispatch('getServerProviderRegions', provider);
                serverProviderStore.dispatch('getServerProviderFeatures', provider);
            },
            createServer: function () {
                serverStore.dispatch('createServer', this.getFormData($(this.$el)));
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
                return serverStore.state.available_server_features;
            },
            availableServerLanguages: () => {
                return serverStore.state.available_server_languages;
            },
            availableServerFrameworks: () => {
                return serverStore.state.available_server_frameworks;
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
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
                                    <div class="input-group input-radio">
                                        <div class="input-question">Server Provider</div>
                                        <template v-for="user_server_provider in user_server_providers">
                                            <label>
                                                <input @change="getProviderData(user_server_provider.server_provider.provider_name)"
                                                       type="radio" name="server_provider_id"
                                                       v-model="form.server_provider_id"
                                                       :value="user_server_provider.server_provider_id">
                                                <span class="icon"></span>
                                                {{ user_server_provider.server_provider.name }}
                                            </label>
                                        </template>
                                    </div>
                                    <template v-if="options.length && regions.length && features.length">
                                        <div class="input-group">
                                            <input type="text" id="name" name="name" v-model="form.name" required>
                                            <label for="name"><span class="float-label">Name</span></label>
                                        </div>

                                        <div class="input-group">
                                            <div class="input-question">Server Option</div>

                                            <select v-model="form.server_option" name="server_option">
                                                <option v-for="option in options" :value="option.id">{{ option.memory }}
                                                    MB
                                                    RAM - {{ option.cpus }} CPUS - {{ option.space }} SSD - ${{
                                                    option.priceHourly }} / Hour - ${{ option.priceMonthly }} / Month
                                                </option>
                                            </select>
                                        </div>

                                        <div class="input-group">
                                            <div class="input-question">Server Region</div>

                                            <select name="server_region" v-model="form.server_region">
                                                <option v-for="region in regions" :value="region.id">{{ region.name }}
                                                </option>
                                            </select>
                                        </div>

                                        <div class="input-group input-checkbox">
                                            <div class="input-question">Server Options</div>
                                            <template v-for="feature in features">
                                                <label>
                                                    <input type="checkbox" name="features[]" :value="feature.id"
                                                           v-model="form.server_features">
                                                    <span class="icon"></span>{{ 'Enable ' + feature.feature }}
                                                    <small>{{ feature.cost }}</small>
                                                </label>
                                            </template>
                                        </div>
                                        <div class="btn-footer">
                                            <button class="btn">Cancel</button>
                                            <button type="submit" class="btn btn-primary">Create Server</button>
                                        </div>

                                        <div v-for="(features, serverFeatureArea) in availableServerFeatures">
                                            <div class="input-group input-checkbox">
                                                <div class="input-question">{{ serverFeatureArea }}</div>
                                                <label  v-for="feature in features">
                                                    <input type="checkbox">
                                                    <span class="icon"></span>{{ feature.name }}
                                                    <template v-if="feature.parameters" v-for="parameter in feature.parameters">
                                                        <input type="text"> {{ parameter }}
                                                    </template>
                                                </label>
                                            </div>
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
    export default {
        components: {
            LeftNav
        },
        data() {
            return {
                form: {
                    name: null,
                    server_option: null,
                    server_region: null,
                    server_features: [],
                    server_provider_id: null
                }
            }
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
                this.form['pile_id'] = this.pile.id;
                serverStore.dispatch('createServer', this.form);
            }
        },
        computed: {
            user_server_providers: () => {
                var providers = serverProviderStore.state.user_server_providers;
                if (providers.length == 1) {
                }
                return providers;
            },
            options: () => {
                return serverProviderStore.state.server_provider_options;
            },
            regions: () => {
                return serverProviderStore.state.server_provider_regions;
            },
            features: () => {
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
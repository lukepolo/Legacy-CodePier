<template>
    <section>
        <left-nav></left-nav>
        <section id="middle" class="section-column">
            <h3 class="section-header primary">Create New Server</h3>

            <div class="section-content">
                <div class="container">
                    <div class="jcf-form-wrap">
                        <form @submit.prevent="createServer()" class="validation-form floating-labels">

                            <template v-if="siteId">
                                <input type="hidden" name="site" :value="siteId">
                            </template>
                            <template v-else>
                                <input type="hidden" name="pile_id" :value="pile">
                            </template>

                            <div class="input-group input-radio">
                                <div class="input-question">Server Provider</div>
                                <template v-if="user_server_providers.length">
                                    <template v-for="user_server_provider in user_server_providers">
                                        <label>
                                            <input
                                                @change="getProviderData(user_server_provider.server_provider_id)"
                                                type="radio"
                                                name="server_provider_id"
                                                :value="user_server_provider.server_provider_id"
                                                v-model="server_provider"
                                            >
                                            <span class="icon"></span>
                                            {{ getServerProviderName(user_server_provider.server_provider_id) }}
                                        </label>
                                    </template>
                                </template>
                                <template v-else>
                                    You can connect other server providers through your
                                    <router-link :to="{ name : 'user_server_providers' }">
                                        profile
                                    </router-link>
                                </template>

                                <label>
                                    <input type="radio" @click="is_custom=true" value="" v-model="server_provider">
                                    <span class="icon"></span>
                                    Custom Server
                                    <template v-if="is_custom">
                                        <input type="hidden" name="custom" value="true">
                                    </template>
                                    <small>
                                        This must be a fresh Ubuntu 16.04 system
                                    </small>
                                </label>

                            </div>

                            <template v-if="is_custom || server_provider">

                                <div class="jcf-input-group">
                                    <input type="text" id="server_name" name="server_name" required>
                                    <label for="server_name"><span class="float-label">Name</span></label>
                                </div>

                                <div class="jcf-input-group" v-if="is_custom">
                                    <input type="number" name="port" required value="22">
                                    <label for="port">
                                        <tooltip message="We will use this port ssh connections" size="medium">
                                            <span class="fa fa-info-circle"></span>
                                        </tooltip>
                                        <span class="float-label">SSH Port</span>
                                    </label>
                                </div>

                                <template v-if="server_provider && server_options.length && server_regions.length && server_provider_features.length">

                                    <div class="jcf-input-group">
                                        <div class="input-question">Size</div>

                                        <div class="select-wrap">
                                            <select name="server_option">
                                                <option v-for="option in server_options" :value="option.id">
                                                    {{ option.memory }} MB RAM
                                                    - {{ option.cpus }} CPUS
                                                    - {{ option.space }} SSD
                                                    - ${{ option.priceHourly }} / Hour
                                                    - ${{ option.priceMonthly }} / Month
                                                </option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="jcf-input-group">
                                        <div class="input-question">Region</div>

                                        <div class="select-wrap">
                                            <select name="server_region">
                                                <option v-for="region in server_regions" :value="region.id">{{ region.name }}</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="input-group input-checkbox">
                                        <div class="input-question">Features</div>
                                        <template v-for="feature in server_provider_features">
                                            <label>
                                                <input
                                                    type="checkbox"
                                                    name="server_provider_features[]"
                                                    :value="feature.id"
                                                >
                                                <span class="icon"></span>{{ 'Enable ' + feature.feature }}
                                                <small>{{ feature.cost }}</small>
                                            </label>
                                        </template>
                                    </div>
                                </template>

                                <hr></hr>

                                <div class="jcf-input-group">
                                    <label for="web_directory">
                                        <tooltip message="We have configured your site based on your apps language and framework, thus you do not need to modify the server if you do not want to" size="medium">
                                            <span class="fa fa-info-circle"></span>
                                        </tooltip>
                                        <h3 v-if="$route.params.site_id">Your server has been customized for your application</h3>
                                    </label>
                                </div>

                                <server-features :update="false"></server-features>

                                <div class="btn-footer">
                                    <button type="submit" class="btn btn-primary">Create Server</button>
                                </div>

                            </template>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </section>
</template>

<script>
    import LeftNav from '../../core/LeftNav.vue';
    import {ServerFeatures} from '../../core/setup/pages'

    export default {
        components: {
            LeftNav,
            ServerFeatures
        },
        created() {
            this.fetchData();
        },
        watch: {
            '$route': 'fetchData'
        },
        data() {
         return {
            is_custom : false,
            server_provider : null
         }
        },
        methods: {
            fetchData() {
                this.$store.dispatch('server_providers/get');
                this.$store.dispatch('user_server_providers/get');
            },
            getProviderData(server_provider_id) {
                this.is_custom = false
                let provider = _.find(this.server_providers, { id : server_provider_id}).provider_name;
                if(provider) {
                    this.$store.dispatch('server_providers/getFeatures', provider);
                    this.$store.dispatch('server_providers/getOptions', provider);
                    this.$store.dispatch('server_providers/getRegions', provider);
                }
            },
            createServer() {
                this.$store.dispatch('user_servers/store', this.getFormData(this.$el)).then((server) => {
                    if(server.id) {
                        if (this.siteId) {
                            app.$router.push({ name : 'site_repository', params : { site_id : this.siteId}})
                        } else {
                            app.$router.push('/')
                        }
                    }
                })
            },
            getServerProviderName(server_provider_id) {
                if(this.server_providers) {
                    let server_provider = _.find(this.server_providers, { id : server_provider_id})
                    if(server_provider) {
                        return server_provider.name
                    }
                }
            }
        },
        computed: {
            pile() {
                return this.$store.state.user.user.current_pile_id
            },
            siteId() {
                return this.$route.params.site_id
            },
            server_options() {
                return this.$store.state.server_providers.options
            },
            server_regions() {
                return _.sortBy(this.$store.state.server_providers.regions, 'name')
            },
            server_providers() {
                return this.$store.state.server_providers.providers
            },
            user_server_providers() {
                return this.$store.state.user_server_providers.providers
            },
            server_provider_features() {
                return this.$store.state.server_providers.features
            }
        }
    }
</script>
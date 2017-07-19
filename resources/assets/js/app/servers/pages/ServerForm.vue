<template>
    <section>
        <left-nav></left-nav>
        <section id="middle" class="section-column">
            <h3 class="section-header primary">Create New Server</h3>

            <div class="section-content">
                <div class="container">
                    <form @submit.prevent="createServer()" class="validation-form floating-labels">

                        <template v-if="siteId">
                            <input type="hidden" name="site" :value="siteId">
                        </template>
                        <template v-else>
                            <input type="hidden" name="pile_id" :value="pile">
                        </template>

                        <template v-if="$route.params.type">
                            <input type="hidden" name="type" :value="$route.params.type">
                        </template>


                        <server-provider-selector :server_provider_id.sync="server_provider_id" :is_custom.sync="is_custom"></server-provider-selector>

                        <template v-if="is_custom || server_provider_id">

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

                            <template v-if="server_provider_id && server_options.length && server_regions.length">

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

                                <div class="input-group input-checkbox" v-if="server_provider_features.length">
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

                            <div class="jcf-input-group">
                                <label for="web_directory">
                                    <h3 v-if="$route.params.site_id">
                                        <tooltip message="We have configured your site based on your apps language and framework, thus you do not need to modify the server if you do not want to" size="medium">
                                            <span class="fa fa-info-circle"></span>
                                        </tooltip>
                                        Your server has been customized for your application<br>
                                        <small>
                                            <a @click="customize_server = !customize_server">(customize)</a>
                                        </small>
                                    </h3>
                                    <h3 v-else>
                                        Setup your server :
                                    </h3>
                                </label>
                            </div>

                            <server-features :update="false" v-show="customize_server"></server-features>

                            <br><br><br>
                            <div class="btn-footer">
                                <button type="submit" class="btn btn-primary">Create Server</button>
                            </div>

                        </template>
                    </form>
                </div>
            </div>
        </section>
    </section>
</template>

<script>

    import {ServerFeatures} from '../../setup/pages'
    import LeftNav from '../../../components/LeftNav.vue'
    import ServerProviderSelector from './../components/ServerProviderSelector.vue'
    export default {
        components: {
            LeftNav,
            ServerFeatures,
            ServerProviderSelector
        },
        data() {
         return {
            is_custom : false,
            server_provider_id : null,
            customize_server : !this.$route.params.site_id,
         }
        },
        watch : {
            'server_provider_id' : function() {
                if(this.server_provider_id) {
                    this.getProviderData(this.server_provider_id)
                }
            }
        },
        methods: {
            getProviderData(server_provider_id) {
                this.is_custom = false
                let provider = _.find(this.server_providers, { id : server_provider_id}).provider_name
                if(provider) {
                    this.$store.dispatch('server_providers/getFeatures', provider)
                    this.$store.dispatch('server_providers/getOptions', provider)
                    this.$store.dispatch('server_providers/getRegions', provider)
                }
            },
            createServer() {
                this.$store.dispatch('user_servers/store', this.getFormData(this.$el)).then((server) => {
                    if(server.id) {
                        if (this.siteId) {
                            app.$router.push({ name : 'site_overview', params : { site_id : this.siteId}})
                        } else {
                            app.$router.push('/')
                        }
                    }
                })
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
            server_provider_features() {
                return this.$store.state.server_providers.features
            }
        }
    }
</script>
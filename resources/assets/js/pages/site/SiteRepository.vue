<template>
    <section>
        <left-nav></left-nav>
        <transition name="swap">
            <section id="middle" class="section-column">
                <site-header></site-header>
                <div class="section-content" v-if="site">
                    <div class="container">
                        <site-nav></site-nav>



                        <div class="jcf-form-wrap">
                            <form @submit.prevent="updateSite" class="floating-labels">
                                <div class="jcf-input-group">
                                    <input type="text" v-model="form.repository" name="repository">
                                    <label for="repository">
                                        <span class="float-label">
                                            Repository
                                        </span>
                                    </label>
                                </div>
                            </form>
                        </div>


                            <!---->
                            <!---->

                            <!--<label>Branch</label>-->
                            <!--<input type="text" v-model="form.branch" name="branch">-->

                            <!--<label>Web Directory</label>-->
                            <!--<input type="text" name="web_directory" v-model="form.web_directory">-->
                            <!--<label>-->
                                <!--<input type="checkbox" v-model="form.zerotime_deployment" name="zerotime_deployment"-->
                                       <!--value="1">-->
                                <!--Zerotime Deployment-->
                            <!--</label>-->

                            <!--<label>-->
                                <!--<input type="checkbox" v-model="form.wildcard_domain" name="wildcard_domain" value="1">-->
                                <!--Wildcard Domain-->
                            <!--</label>-->

                            <!--<div class="form-group">-->
                                <!--<div class="radio" v-for="user_repository_provider in user_repository_providers">-->
                                    <!--<label>-->
                                        <!--<input name="user_repository_provider_id" type="radio"-->
                                               <!--v-model="form.user_repository_provider_id"-->
                                               <!--:value="user_repository_provider.id">-->
                                        <!--{{ user_repository_provider.repository_provider.name }}-->
                                    <!--</label>-->
                                <!--</div>-->
                            <!--</div>-->

                            <!--<div class="form-group">-->
                                <!--<select v-model="form.framework" name="framework">-->
                                    <!--<option></option>-->
                                    <!--<optgroup :label="language" v-for="(features, language) in availableLanguages">-->
                                        <!--<option v-for="(features, framework) in availableFrameworks[language]"-->
                                                <!--:value="language+'.'+framework"> {{ framework }}-->
                                        <!--</option>-->
                                    <!--</optgroup>-->
                                <!--</select>-->
                            <!--</div>-->

                            <!--<button type="submit">Update Repository</button>-->
                        <!--</form>-->

                        <template v-if="site.repository && site_servers.length">
                            <a href="#" @click.prevent="deploySite(site.id)" class="btn btn-primary">Deploy</a>
                            <a v-if="!site.automatic_deployment_id" href="#" class="btn btn-primary">Start Automatic
                                Deployments</a>
                            <a v-else href="#" class="btn btn-primary">Stop Automatic Deployments</a>
                        </template>
                        <div @click="deleteSite(site.id)" class="btn btn-xs">Delete Site</div>
                    </div>
                </div>
            </section>
        </transition>
        <servers></servers>
    </section>
</template>

<script>

    import LeftNav from './../../core/LeftNav.vue';
    import SiteNav from './components/SiteNav.vue';
    import Servers from './components/Servers.vue';
    import SiteHeader from './components/SiteHeader.vue';
    export default {
        components: {
            SiteHeader,
            SiteNav,
            LeftNav,
            Servers
        },
        data() {
            return {
                form: {
                    branch: null,
                    framework: null,
                    repository: null,
                    web_directory: null,
                    zerotime_deployment: null,
                    user_repository_provider_id: null
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
            fetchData() {
                this.$store.dispatch('getUserRepositoryProviders');
                this.$store.dispatch('getSite', this.$route.params.site_id);
                this.$store.dispatch('getServerAvailableFrameworks');
                this.$store.dispatch('getServerAvailableLanguages');
            },
            deploySite: function (site_id) {
                Vue.http.post(this.action('Site\SiteController@deploy', {site: site_id}));
            },
            updateSite() {
                this.$store.dispatch('updateSite', {
                    site_id: this.site.id,
                    data: {
                        branch: this.form.branch,
                        domain: this.site.domain,
                        pile_id: this.site.pile_id,
                        framework: this.form.framework,
                        repository: this.form.repository,
                        web_directory: this.form.web_directory,
                        wildcard_domain: this.form.wildcard_domain,
                        zerotime_deployment: this.form.zerotime_deployment,
                        user_repository_provider_id: this.form.user_repository_provider_id
                    }
                });
            },
            deleteSite: function (site_id) {
                this.$store.dispatch('deleteSite', site_id);
            }
        },
        computed: {
            site() {
                var site = this.$store.state.sitesStore.site;

                if (site) {
                    this.form.branch = site.branch;
                    this.form.framework = site.framework;
                    this.form.repository = site.repository;
                    this.form.web_directory = site.web_directory;
                    this.form.zerotime_deployment = (site.zerotime_deployment ? true : false);
                    this.form.user_repository_provider_id = site.user_repository_provider_id;
                }

                return site;
            },
            user_repository_providers() {
                return this.$store.state.userStore.repository_providers;
            },
            availableLanguages() {
                return this.$store.state.serversStore.available_server_languages;
            },
            availableFrameworks() {
                return this.$store.state.serversStore.available_server_frameworks;
            },
            site_servers() {
                return [];
            }
        },
    }
</script>
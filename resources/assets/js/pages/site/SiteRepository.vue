<template>
    <section>
        <div class="section-content" v-if="site">
            <div class="container">
                <div class="jcf-form-wrap">
                    <form @submit.prevent="updateSite" class="floating-labels">
                        <div class="jcf-input-group">
                            <input type="text" v-model="form.repository" name="repository">
                            <label for="repository">
                                <span class="float-label">Repository Name</span>
                            </label>
                        </div>
                        <div class="jcf-input-group">
                            <input type="text" v-model="form.branch" name="branch">
                            <label for="branch">
                                <span class="float-label">Branch</span>
                            </label>
                        </div>
                        <div class="jcf-input-group">
                            <input type="text" name="web_directory" v-model="form.web_directory">
                            <label for="web_directory">
                                <span class="float-label">Web Directory</span>
                            </label>
                        </div>
                        <div class="jcf-input-group input-checkbox">
                            <div class="input-question">Repository Options</div>
                            <label>
                                <input type="checkbox" v-model="form.zerotime_deployment" name="zerotime_deployment" value="1">
                                <span class="icon"></span>
                                Zerotime Deployment
                            </label>
                            <label>
                                <input type="checkbox" v-model="form.wildcard_domain" name="wildcard_domain" value="1">
                                <span class="icon"></span>
                                Wildcard Domain
                            </label>
                        </div>
                        <div class="jcf-input-group input-radio">
                            <div class="input-question">Repository Provider</div>
                            <label v-for="user_repository_provider in user_repository_providers">
                                <input name="user_repository_provider_id" type="radio" v-model="form.user_repository_provider_id" :value="user_repository_provider.id">
                                <span class="icon"></span>
                                {{ user_repository_provider.repository_provider.name }}
                            </label>
                        </div>
                        <div class="jcf-input-group">
                            <div class="input-question">Select Framework</div>
                            <!-- TODO allow user to de-select a framework. Have an option of "none" -->
                            <div class="select-wrap">
                                <select v-model="form.framework" name="framework">
                                    <optgroup :label="language" v-for="(features, language) in availableLanguages">
                                        <option v-for="(features, framework) in availableFrameworks[language]" :value="language+'.'+framework"> {{ framework }}></option>
                                    </optgroup>
                                </select>
                            </div>
                        </div>
                    </form>

                    <div class="btn-footer">
                        <button class="btn">Delete Site</button>
                        <button class="btn btn-primary" type="submit">Update Repository</button>
                    </div>
                </div>
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
</template>

<script>

    export default {
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
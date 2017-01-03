<template>
    <div v-if="site">
        <div class="jcf-form-wrap">
            <form @submit.prevent="updateSite" class="floating-labels">
                <h3>Repository</h3>
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
                        {{ getRepositoryName(user_repository_provider.repository_provider_id) }}
                    </label>
                </div>

                <div class="jcf-input-group">
                    <div class="input-question">Select Type</div>
                    <div class="select-wrap">
                        <select v-model="form.type" name="type">
                            <option :value="language" v-for="(features, language) in availableLanguages">
                                {{ language }}
                            </option>
                        </select>
                    </div>
                </div>

                <div class="jcf-input-group">
                    <div class="input-question">Select Framework</div>
                    <div class="select-wrap">
                        <select v-model="form.framework" name="framework">
                            <option>None</option>
                            <optgroup :label="language" v-for="(features, language) in availableLanguages">
                                <option v-for="(features, framework) in availableFrameworks[language]" :value="language+'.'+framework"> {{ framework }}</option>
                            </optgroup>
                        </select>
                    </div>
                </div>
            </form>

            <div class="btn-footer">

                <confirm dispatch="deleteSite" :params="site.id" :confirm_with_text="site.name"> Delete Site </confirm>
                <button @click="updateSite" class="btn btn-primary" type="submit">Update Repository</button>
            </div>
        </div>
        <template v-if="site.repository">

            <template v-if="isDeploying">
                {{ isDeploying.status }}
            </template>

            <a href="#" @click.prevent="deploySite(site.id)" class="btn btn-primary">Deploy</a>


            <template v-if="!site.automatic_deployment_id">
                <a class="btn btn-primary" @click.prevent="createDeployHook">Start AutomaticDeployments</a>
                <template v-if="!site.private">
                    <small>Please make sure you own the public repository otherwise we cannot create the deploy hook</small>
                </template>
            </template>
            <template v-else>
                <a class="btn btn-primary" @click.prevent="removeDeployHook">Stop Automatic Deployments</a>
            </template>
        </template>
    </div>
</template>

<script>

    export default {
        data() {
            return {
                form: {
                    type : null,
                    branch: null,
                    framework: null,
                    repository: null,
                    web_directory: null,
                    wildcard_domain : false,
                    zerotime_deployment: true,
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
                this.$store.dispatch('getRepositoryProviders');
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
                        type : this.form.type,
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
            createDeployHook() {
                return this.$store.dispatch('createDeployHook', this.site.id);
            },
            removeDeployHook() {
                this.$store.dispatch('removeDeployHook', {
                    site : this.site.id,
                    hook : this.site.automatic_deployment_id
                });
            },
            getRepositoryName(user_repository_id) {
                if(this.repository_providers) {
                    let repository = _.find(this.repository_providers, {id : user_repository_id});
                    if(repository) {
                        return repository.name;
                    }
                }

            }
        },
        computed: {
            site() {
                let site = this.$store.state.sitesStore.site;

                if (site) {
                    this.form.type = site.type;
                    this.form.branch = site.branch;
                    this.form.framework = site.framework;
                    this.form.repository = site.repository;
                    this.form.web_directory = site.web_directory;
                    this.form.wildcard_domain = site.wildcard_domain;
                    this.form.zerotime_deployment = site.zerotime_deployment;
                    this.form.user_repository_provider_id = site.user_repository_provider_id;
                }

                return site;
            },
            repository_providers() {
                return this.$store.state.userStore.repository_providers;
            },
            user_repository_providers() {
                return this.$store.state.userStore.user_repository_providers;
            },
            availableLanguages() {
                return this.$store.state.serversStore.available_server_languages;
            },
            availableFrameworks() {
                return this.$store.state.serversStore.available_server_frameworks;
            },
            site_servers() {
                return [];
            },
            isDeploying() {
                if(this.site) {
                    return _.find(this.$store.state.sitesStore.running_deployments[this.site.id], function(deployment) {
                        return deployment.status != 'Completed' && deployment.status != 'Failed';
                    });
                }

                return false;
            }
        },
    }
</script>
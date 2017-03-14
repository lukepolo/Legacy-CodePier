<template>
    <div v-if="site">

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
                        <tooltip message="The location of your apps entry (ex : public) no need for leading '/'" size="medium">
                            <span class="fa fa-info-circle"></span>
                        </tooltip>
                        <span class="float-label">Web Directory</span>
                    </label>
                </div>

                <div class="jcf-input-group input-checkbox">
                    <div class="input-question">Repository Options</div>
                    <label>
                        <tooltip message="Your app can be deployed in zerotime deployment, we suggest you go for it!" size="medium">
                            <span class="fa fa-info-circle"></span>
                        </tooltip>
                        <input type="checkbox" v-model="form.zerotime_deployment" name="zerotime_deployment" value="1">
                        <span class="icon"></span>
                        Zerotime Deployment
                    </label>
                </div>

                <template v-if="form.zerotime_deployment">
                    <div class="jcf-input-group">
                        <input type="number" v-model="form.keep_releases" name="keep_releases">
                        <label for="keep_releases">
                            <tooltip message="When using zerotime deployments you can keep a number of releases, if set to zero we will keep them all" size="medium">
                                <span class="fa fa-info-circle"></span>
                            </tooltip>
                            <span class="float-label">Number of Releases to keep</span>
                        </label>
                    </div>
                </template>

                <div class="jcf-input-group input-checkbox">
                    <label>
                        <tooltip message="If your site requires a wildcard (ex : *.codepier.io) you should check this" size="medium">
                            <span class="fa fa-info-circle"></span>
                        </tooltip>
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
                    <div class="input-question">Select Language</div>
                    <div class="select-wrap">
                        <select v-model="form.type" name="type">
                            <option :value="language" v-for="(features, language) in availableLanguages">
                                {{ language }}
                            </option>
                        </select>
                    </div>
                </div>

                <div class="jcf-input-group">
                    <tooltip message="By selecting a framework, we customize the options surrounding your app" size="medium">
                        <span class="fa fa-info-circle"></span>
                    </tooltip>
                    <div class="input-question">Select Framework</div>
                    <div class="select-wrap">
                        <select v-model="form.framework" name="framework">
                            <option value="">None</option>
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

        <template v-if="site.repository && hasDeployableServers">

            <a href="#" @click.prevent="deploySite" :class="{ 'btn-disabled' : isDeploying }">
                <span class="icon-deploy"></span>
                <template v-if="isDeploying">
                    {{ isDeploying.status }}
                </template>
            </a>

            <template v-if="!site.automatic_deployment_id">
                <a @click.prevent="createDeployHook"><span class="icon-cloud-auto-deploy"></span></a>
            </template>
            <template v-else>
                <a @click.prevent="removeDeployHook"><span class="icon-cloud-auto-deploy"></span></a>
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
                    keep_releases : null,
                    wildcard_domain : false,
                    zerotime_deployment: true,
                    user_repository_provider_id: null
                }
            }
        },
        created() {
            this.fetchData()
        },
        watch: {
            '$route': 'fetchData'
        },
        methods: {
            fetchData() {
                this.$store.dispatch('getRepositoryProviders')
                this.$store.dispatch('getUserRepositoryProviders')
                this.$store.dispatch('getSite', this.$route.params.site_id)
                this.$store.dispatch('getServerAvailableFrameworks')
                this.$store.dispatch('getServerAvailableLanguages')
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
                        keep_releases : this.form.keep_releases,
                        wildcard_domain: this.form.wildcard_domain,
                        zerotime_deployment: this.form.zerotime_deployment,
                        user_repository_provider_id: this.form.user_repository_provider_id
                    }
                });
            },
            createDeployHook() {
                return this.$store.dispatch('createDeployHook', this.site.id)
            },
            removeDeployHook() {
                this.$store.dispatch('removeDeployHook', {
                    site : this.site.id,
                    hook : this.site.automatic_deployment_id
                });
            },
            getRepositoryName(user_repository_id) {
                if(this.repository_providers) {
                    let repository = _.find(this.repository_providers, {id : user_repository_id})
                    if(repository) {
                        return repository.name
                    }
                }

            }
        },
        computed: {
            site() {
                let site = this.$store.state.sitesStore.site;

                if (site) {
                    this.form.type = site.type
                    this.form.branch = site.branch
                    this.form.framework = site.framework
                    this.form.repository = site.repository
                    this.form.keep_releases = site.keep_releases
                    this.form.web_directory = site.web_directory
                    this.form.wildcard_domain = site.wildcard_domain
                    this.form.zerotime_deployment = site.zerotime_deployment
                    this.form.user_repository_provider_id = site.user_repository_provider_id
                }

                return site;
            },
            repository_providers() {
                return this.$store.state.userStore.repository_providers
            },
            user_repository_providers() {
                return this.$store.state.userStore.user_repository_providers
            },
            availableLanguages() {
                return this.$store.state.serversStore.available_server_languages
            },
            availableFrameworks() {
                return this.$store.state.serversStore.available_server_frameworks
            }
        },
    }
</script>
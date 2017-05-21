<template>
    <div v-if="site">
        <div class="jcf-form-wrap" v-if="user_repository_providers.length">

            <h3>
                To create your site, first we need to select a repository provider.
            </h3>

            <form @submit.prevent="updateSite" class="floating-labels">

                <div class="jcf-input-group input-radio">

                    <div class="input-question">Repository Provider</div>

                    <label v-for="user_repository_provider in user_repository_providers">

                        <input name="user_repository_provider_id" type="radio" v-model="form.user_repository_provider_id" :value="user_repository_provider.id">
                        <span class="icon"></span>
                        {{ getRepositoryName(user_repository_provider.repository_provider_id) }}

                    </label>

                </div>

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

                <div class="jcf-input-group">

                    <div class="input-question">Select Language</div>

                    <div class="select-wrap">

                        <select v-model="form.type" name="type">
                            <option value=""></option>
                            <option :value="language" v-for="(features, language) in availableLanguages">
                                {{ language }}
                            </option>
                        </select>

                    </div>

                </div>

                <div class="jcf-input-group" v-if="form.type">

                    <tooltip message="By selecting a framework, we customize the options surrounding your app" size="medium">
                        <span class="fa fa-info-circle"></span>
                    </tooltip>

                    <div class="input-question">Select Framework</div>

                    <div class="select-wrap">

                        <select v-model="form.framework" name="framework">
                            <option value="">None</option>
                            <option v-for="(features, framework) in availableFrameworks[form.type]" :value="form.type+'.'+framework"> {{ framework }}</option>
                        </select>

                    </div>

                </div>

            </form>


            <div class="btn-footer">
                <confirm dispatch="user_sites/destroy" :params="site.id" :confirm_with_text="site.name"> Delete Site </confirm>
                <button @click="updateSite" class="btn btn-primary" type="submit">Update Repository</button>
            </div>

        </div>

        <div v-else>

            You do not have any connected repsoitory providers, please
            <router-link :to="{ name : 'user_repository_providers' }">
                connect a repository provider
            </router-link>

        </div>
    </div>
</template>

<script>

    export default {
        data() {
            return {
                form: {
                    type : null,
                    branch: 'master',
                    framework: null,
                    repository: null,
                    web_directory: 'public',
                    keep_releases : 10,
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
                this.$store.dispatch('server_languages/get')
                this.$store.dispatch('server_frameworks/get')
                this.$store.dispatch('repository_providers/get')
            },
            updateSite() {
                this.$store.dispatch('user_sites/update', {
                    site: this.site.id,
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
                let site = this.$store.state.user_sites.site;

                if (site && site.repository) {
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
                return this.$store.state.repository_providers.providers
            },
            user_repository_providers() {
                return this.$store.state.user_repository_providers.providers
            },
            availableLanguages() {
                return this.$store.state.server_languages.languages
            },
            availableFrameworks() {
                return this.$store.state.server_frameworks.frameworks
            }
        },
    }
</script>
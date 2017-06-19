<template>
    <div v-if="site">
        <h3>To create your site, you need to select a source control provider.</h3>

        <form @submit.prevent="updateSite">

            <div class="jcf-input-group input-radio">

                <div class="input-question">Source Control Provider</div>

                <repository-provider-selector :provider.sync="form.user_repository_provider_id"></repository-provider-selector>

                <div class="btn btn-default" @click="form.custom_provider = true">
                    Custom
                </div>

            </div>

            <template v-if="form.user_repository_provider_id || form.custom_provider">

                <div class="flyform--group">
                    <div class="flyform--group-prefix">
                        <input type="text" v-model="form.repository" name="repository" placeholder=" " required>
                        <label for="repository">Repository URL</label>
                        <template v-if="!form.custom_provider">
                            <div class="flyform--group-prefix-label">
                                {{ providerUrl }}/
                            </div>
                        </template>
                    </div>

                    <template v-if="!form.custom_provider">
                        <div class="flyform--input-icon-right">
                            <a target="_blank" :href="'http://'+repositoryUrl"><span class="icon-link"></span></a>
                        </div>
                    </template>
                </div>

                <template v-if="form.custom_provider">
                    <div class="flyform--input-text">
                        Please enter the full URL to your repository
                    </div>
                </template>

                <div class="flyform--group">
                    <input type="text" v-model="form.branch" name="branch" placeholder=" ">
                    <label for="branch">Branch</label>
                </div>

                <div class="flyform--group">
                    <div class="flyform--group-prefix">
                        <input type="text" name="web_directory" v-model="form.web_directory" placeholder=" ">
                        <label for="web_directory" class="flyform--group-iconlabel">Web Directory</label>
                        <div class="flyform--group-prefix-label">
                            ~/codepier/{{site.domain}}/
                        </div>
                        <tooltip message="The location of your apps entry (ex : public)" size="medium">
                            <span class="fa fa-info-circle"></span>
                        </tooltip>

                    </div>
                </div>

                <div class="flyform--group">
                    <label>Language & Framework</label>
                    <div class="flyform--group-select">

                        <select v-model="form.type" name="type" required>
                            <option value=""></option>
                            <template v-for="(features, language) in availableLanguages">
                                <optgroup :label="language">
                                    <option :value="language">
                                        Generic {{ language }}
                                    </option>
                                    <option v-for="(features, framework) in availableFrameworks[language]" :value="language+'.'+framework"> {{ framework }}</option>
                                </optgroup>
                            </template>
                        </select>

                    </div>
                </div>

                <div class="flyform--footer">
                    <div class="flyform--footer-btns">
                        <confirm dispatch="user_sites/destroy" :params="site.id" :confirm_with_text="site.name"> Delete Site </confirm>
                        <button class="btn btn-primary" type="submit" :disabled="form.diff().length === 0">Update Repository</button>
                    </div>
                </div>

                <br><br><br><br>
                <div class="grid-2">
                    <div class="flyform--group">
                        <label>
                            <span>Repository Options</span>
                        </label>

                        <label>
                            <tooltip message="Your app can be deployed in zerotime deployment, we suggest you go for it!" size="medium">
                                <span class="fa fa-info-circle"></span>
                            </tooltip>

                            <input type="checkbox" v-model="form.zerotime_deployment" name="zerotime_deployment" value="1">
                            <span class="icon"></span>
                            Zerotime Deployment
                        </label>
                    </div>

                    <div class="flyform--group">
                        <input type="number" v-model="form.keep_releases" name="keep_releases" placeholder=" ">

                        <label for="keep_releases" class="flyform--group-iconlabel">
                            <span>Number of Releases to keep</span>
                        </label>

                        <tooltip message="When using zerotime deployments you can keep a number of releases, if set to zero we will keep them all" size="medium">
                            <span class="fa fa-info-circle"></span>
                        </tooltip>

                    </div>
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

                        <tooltip :message="'If your site requires a wildcard (ex : *.'+ site.domain +') you should check this'" size="medium">
                            <span class="fa fa-info-circle"></span>
                        </tooltip>

                        <input type="checkbox" v-model="form.wildcard_domain" name="wildcard_domain" value="1">
                        <span class="icon"></span>
                        Wildcard Domain

                    </label>

                </div>

                <div class="jcf-input-group">

                    <div class="input-question">Language & Framework</div>

                    <div class="select-wrap">

                        <select v-model="form.type" name="type" required>
                            <option value=""></option>
                            <template v-for="(features, language) in availableLanguages">
                                <optgroup :label="language">
                                    <option :value="language">
                                        Generic {{ language }}
                                    </option>
                                    <option v-for="(features, framework) in availableFrameworks[language]" :value="language+'.'+framework"> {{ framework }}</option>
                                </optgroup>
                            </template>
                        </select>

                    </div>

                </div>

            </template>



        </form>


    </div>
</template>

<script>

    import RepositoryProviderSelector from './../components/RepositoryProviderSelector.vue'

    export default {
        components: {
            RepositoryProviderSelector
        },
        data() {
            return {
                form: this.createForm({
                    type : null,
                    branch: 'master',
                    framework: null,
                    repository: null,
                    keep_releases : 10,
                    web_directory: 'public',
                    custom_provider : false,
                    wildcard_domain : false,
                    zerotime_deployment: true,
                    user_repository_provider_id: null
                })
            }
        },
        mounted() {
            this.fetchData()
            this.siteChange()
        },
        watch: {
            '$route': 'fetchData',
            'site' : 'siteChange',
            '$data.form.user_repository_provider_id' : function(value) {
                if(value) {
                    this.form.custom_provider = false
                }
            },
            '$data.form.custom_provider' : function(value) {
                if(value) {
                    Vue.set(this.form, 'user_repository_provider_id', null)
                }
            }
        },
        methods: {
            fetchData() {
                this.$store.dispatch('server_languages/get')
                this.$store.dispatch('server_frameworks/get')
                this.$store.dispatch('repository_providers/get')
                this.$store.dispatch('user_repository_providers/get', this.$store.state.user.user.id)
            },
            siteChange() {

                this.form.empty()
                
                let site = this.site

                if (site && site.repository) {

                    this.form.type = site.framework ? site.framework : site.type
                    this.form.branch = site.branch
                    this.form.repository = site.repository
                    this.form.keep_releases = site.keep_releases
                    this.form.web_directory = site.web_directory
                    this.form.wildcard_domain = site.wildcard_domain
                    this.form.zerotime_deployment = site.zerotime_deployment
                    this.form.user_repository_provider_id = site.user_repository_provider_id

                    if(this.form.repository && !this.form.user_repository_provider_id) {
                        this.form.custom_provider = true
                    }

                }

                this.form.setOriginalData()

            },
            updateSite() {

                let tempType = _.split(this.form.type, '.')
                let type = tempType[0]
                let framework = null
                if(tempType.length === 2) {
                    framework = tempType[0]+'.'+tempType[1]
                }

                this.$store.dispatch('user_sites/update', {
                    site: this.site.id,
                    type : type,
                    branch: this.form.branch,
                    domain: this.site.domain,
                    pile_id: this.site.pile_id,
                    framework: framework,
                    repository: this.form.repository,
                    web_directory: this.form.web_directory,
                    keep_releases : this.form.keep_releases,
                    wildcard_domain: this.form.wildcard_domain,
                    custom_provider : this.form.custom_provider,
                    zerotime_deployment: this.form.zerotime_deployment,
                    user_repository_provider_id: this.form.user_repository_provider_id
                });
            },
        },
        computed: {
            site() {
                return this.$store.state.user_sites.site;
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
            },
            providerUrl() {
                if(this.form.user_repository_provider_id) {

                    let userRepository = _.find(this.user_repository_providers, {
                        id : this.form.user_repository_provider_id
                    })

                    if(userRepository) {
                        let repositoryProvider = _.find(this.repository_providers, {
                            id : userRepository.repository_provider_id
                        })

                        if(repositoryProvider) {
                            return repositoryProvider.url
                        }
                    }

                }
            },
            repositoryUrl() {
                return this.providerUrl + '/' + (this.form.repository ? this.form.repository : '')
            }
        },
    }
</script>
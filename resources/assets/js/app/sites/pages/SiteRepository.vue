<template>
    <div v-if="site">
        <h3 class="heading" v-if="!form.user_repository_provider_id && !form.custom_provider">
            To create your site, you need to select a source control provider.
        </h3>

        <form @submit.prevent="updateSite">

            <repository-provider-selector :provider.sync="form.user_repository_provider_id">
                <div class="providers--item providers--item-custom" @click="form.custom_provider = true">
                    <div class="providers--item-header">
                        <div class="providers--item-name"><h3>Custom</h3></div>
                    </div>
                </div>
            </repository-provider-selector>

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
                        <tooltip message="The location of your apps entry" size="medium">
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
                        <button class="btn btn-primary" type="submit" :disabled="form.diff().length === 0">Update Repository</button>
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
                    web_directory: 'public',
                    custom_provider : false,
                    user_repository_provider_id: null
                })
            }
        },
        created() {
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
                    this.form.web_directory = site.web_directory
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
                    custom_provider : this.form.custom_provider,
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
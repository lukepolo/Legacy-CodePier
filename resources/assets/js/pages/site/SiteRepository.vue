<template>
    <section>
        <left-nav></left-nav>
        <section id="middle" class="section-column">
            <h3 class="section-header primary">Site Repository</h3>
            <div class="section-content" v-if="site">
                <div class="container">
                    <site-nav :site="site"></site-nav>

                    <form @submit.prevent="updateSite">
                        <label>Repository</label>
                        <input type="text" v-model="repository" name="repository">

                        <label>Branch</label>
                        <input type="text" v-model="branch" name="branch">

                        <label>Web Directory</label>
                        <input type="text" name="web_directory" v-model="web_directory">
                        <label>
                            <input type="checkbox" v-model="zerotime_deployment" name="zerotime_deployment" value="1">
                            Zerotime Deployment
                        </label>

                        <div class="form-group">
                            <div class="radio" v-for="user_repository_provider in user_repository_providers">
                                <label>
                                    <input name="user_repository_provider_id" type="radio"
                                           v-model="user_repository_provider_id" :value="user_repository_provider.id">
                                    {{ user_repository_provider.repository_provider.name }}
                                </label>
                            </div>
                        </div>

                        <button type="submit">Update Repository</button>
                    </form>

                    <a href="#">Remove Repository</a>

                    <a href="#" class="btn btn-primary">Deploy</a>

                    <a v-if="!site.automatic_deployment_id" href="#" class="btn btn-primary">Start Automatic
                        Deployments</a>
                    <a v-else href="#" class="btn btn-primary">Stop Automatic Deployments</a>

                    <a href="#" class="btn btn-xs">Delete Site</a>
                </div>
            </div>
        </section>
    </section>
</template>

<script>
    import LeftNav from './../../core/LeftNav.vue';
    import SiteNav from './components/SiteNav.vue';
    export default {
        components: {
            SiteNav,
            LeftNav,
        },
        data() {
            return {
                branch: null,
                repository: null,
                web_directory: null,
                zerotime_deployment: null,
                user_repository_provider_id: null
            }
        },
        computed: {
            site: function () {
                var site = siteStore.state.site;

                if (site) {
                    this.branch = site.branch;
                    this.repository = site.repository;
                    this.web_directory = site.web_directory;
                    this.zerotime_deployment = (site.zerotime_deployment ? true : false);
                    this.user_repository_provider_id = site.user_repository_provider_id;
                }

                return site;
            },
            user_repository_providers: () => {
                return userStore.state.repository_providers;
            }
        },
        methods: {
            updateSite: function () {
                siteStore.dispatch('updateSite', {
                    site_id: this.site.id,
                    data: {
                        branch: this.branch,
                        domain : this.site.domain,
                        pile_id : this.site.pile_id,
                        repository: this.repository,
                        web_directory: this.web_directory,
                        wildcard_domain : this.wildcard_domain,
                        zerotime_deployment: this.zerotime_deployment,
                        user_repository_provider_id: this.user_repository_provider_id
                    }
                });
            }
        },
        mounted() {
            userStore.dispatch('getUserRepositoryProviders');
            siteStore.dispatch('getSite', this.$route.params.site_id);
        }
    }
</script>
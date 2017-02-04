<template>
    <div v-if="siteId">
        <div class="tab-container tab-left">
            <ul class="nav nav-tabs">
                <router-link :to="{ name : 'site_repository', params : { site_id : siteId } }" tag="li" exact>
                    <a>Repository
                        <div class="small">Hi this is where yousetup your repository information</div>
                    </a>

                </router-link>

                <router-link :to="{ name : 'site_deployment', params : { site_id : siteId } }" tag="li">
                    <a>Deployment</a>
                </router-link>

                <router-link :to="{ name : 'site_files', params : { site_id : siteId } }" tag="li" v-if="site && site.framework">
                    <a>Framework Files</a>
                </router-link>

                <router-link :to="{ name : 'site_databases', params : { site_id : siteId } }" tag="li" v-if="site">
                    <a>Databases</a>
                </router-link>
            </ul>
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active">
                    <slot></slot>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    export default{
        computed : {
            siteId() {
                return this.$route.params.site_id
            },
            site() {
                return this.$store.state.sitesStore.site
            },
            serverFeatures() {
                return this.$store.state.siteServersFeaturesStore.site_server_features
            }
        }
    }
</script>

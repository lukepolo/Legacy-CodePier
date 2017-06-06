<template>
    <div class="tab-container tab-left">
        <ul class="nav nav-tabs" v-if="site.repository">
            <router-link :to="{ name : 'site_repository', params : { site_id : siteId } }" tag="li" exact>
                <a>
                    Repository
                    <div class="small">Your app's information</div>
                </a>
            </router-link>

            <router-link :to="{ name : 'site_deployment', params : { site_id : siteId } }" tag="li">
                <a>
                    Deployment
                    <div class="small">Customize your app's deployment</div>
                </a>
            </router-link>

            <router-link :to="{ name : 'site_files', params : { site_id : siteId } }" tag="li" v-if="site && site.framework">
                <a>
                    Framework Files
                    <div class="small">Your app has some default files that need to be configured</div>
                </a>
            </router-link>

            <router-link :to="{ name : 'site_databases', params : { site_id : siteId } }" tag="li" v-if="site">
                <a>
                    Databases
                    <div class="small">Setup your site's databases and users</div>
                </a>
            </router-link>
        </ul>
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active">
                <slot></slot>
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
                return this.$store.state.user_sites.site;
            },
        },
        watch: {
            '$route': function() {
                $('#middle .section-content').scrollTop(0)
            }
        }
    }
</script>

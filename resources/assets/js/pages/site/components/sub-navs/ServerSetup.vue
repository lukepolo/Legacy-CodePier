<template>
    <div v-if="siteId">
        <div class="tab-container tab-left">
            <ul class="nav nav-tabs">

                <router-link :to="{ name : 'site_cron_jobs', params : { site_id : siteId } }" tag="li" exact>
                    <a>
                        Cron Jobs
                        <div class="small">Configure cron jobs</div>
                    </a>
                </router-link>

                <router-link :to="{ name : 'site_workers', params : { site_id : siteId } }" tag="li" exact>
                    <a>
                        Workers
                        <div class="small">Configure workers / daemons services</div>
                    </a>
                </router-link>

                <router-link :to="{ name : 'site_server_files', params : { site_id : siteId } }" tag="li">
                    <a>
                        Server Files
                        <div class="small">Customize your server files to suit your app</div>
                    </a>
                </router-link>

                <template v-if="site && !site.repository">
                    <router-link :to="{ name : 'site_repository', params : { site_id : site.id } }" tag="li">
                        <a>
                            Server Features
                            <div class="small">You must setup your sites repository first</div>
                            <div class="small">Advanced server setup, highly customized servers</div>
                        </a>
                    </router-link>
                </template>
                <template v-else>
                    <router-link :to="{ name : 'site_server_features', params : { site_id : siteId } }" tag="li">
                        <a>
                            Server Features
                            <div class="small">Advanced server setup, highly customized servers</div>
                        </a>
                    </router-link>
                </template>

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
            }
        }
    }
</script>

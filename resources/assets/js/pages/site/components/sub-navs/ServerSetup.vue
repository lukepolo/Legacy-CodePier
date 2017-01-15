<template>
    <div v-if="siteId">
        <div class="tab-container tab-left">
            <ul class="nav nav-tabs">

                <router-link :to="{ name : 'site_workers', params : { site_id : siteId } }" tag="li" exact>
                    <a>Workers</a>
                </router-link>

                <router-link :to="{ name : 'site_server_files', params : { site_id : siteId } }" tag="li">
                    <a>Files</a>
                </router-link>

                <template v-if="site && !site.repository">
                    <router-link :to="{ name : 'site_repository', params : { site_id : site.id } }" tag="li">
                        <a>Server Features
                            <small>
                                <br>
                                Setup Repository First
                            </small>
                        </a>
                    </router-link>
                </template>
                <template v-else>
                    <router-link :to="{ name : 'site_server_features', params : { site_id : siteId } }" tag="li">
                        <a>Server Features</a>
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

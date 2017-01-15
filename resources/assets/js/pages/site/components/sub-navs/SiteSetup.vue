<template>
    <div v-if="siteId">
        <div class="tab-container tab-left">
            <ul class="nav nav-tabs">
                <router-link :to="{ name : 'site_repository', params : { site_id : siteId } }" tag="li" exact>
                    <a>Repository</a>
                </router-link>

                <router-link :to="{ name : 'site_deployment', params : { site_id : siteId } }" tag="li">
                    <a>Deployment</a>
                </router-link>

                <router-link :to="{ name : 'site_files', params : { site_id : siteId } }" tag="li">
                    <a>Files</a>
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
            }
        }
    }
</script>

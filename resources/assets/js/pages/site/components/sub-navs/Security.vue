<template>
    <div v-if="siteId">
        <div class="tab-container tab-left">
            <ul class="nav nav-tabs">

                <router-link :to="{ name : 'site_ssh_keys', params : { site_id : siteId } }" tag="li" class="wizard-item">
                    <a>SSH Keys</a>
                </router-link>

                <router-link :to="{ name : 'site_firewall_rules', params : { site_id : siteId } }" tag="li" class="wizard-item">
                    <a>Firewall Rules</a>
                </router-link>

                <router-link :to="{ name : 'site_ssl_certs', params : { site_id : siteId } }" tag="li" v-if="site && site.domain != 'default' ">
                    <a>SSL Certificates</a>
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
                return this.$store.state.sitesStore.site;
            }
        }
    }
</script>
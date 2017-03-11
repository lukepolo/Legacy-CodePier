<template>
    <div v-if="siteId">
        <div class="tab-container tab-left">
            <ul class="nav nav-tabs">

                <router-link :to="{ name : 'site_ssh_keys', params : { site_id : siteId } }" tag="li" class="wizard-item" exact>
                    <a>
                        SSH Keys
                        <div class="small">Add ssh keys that are required to access the server</div>
                    </a>

                </router-link>

                <router-link :to="{ name : 'site_firewall_rules', params : { site_id : siteId } }" tag="li" class="wizard-item">
                    <a>
                        Firewall Rules
                        <div class="small">Setup your apps firewall rules</div>
                    </a>
                </router-link>

                <router-link :to="{ name : 'site_ssl_certs', params : { site_id : siteId } }" tag="li" v-if="site && site.domain != 'default' ">
                    <a>
                        SSL Certificates
                        <div class="small">Configure SSL certificates for your app</div>
                    </a>
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
<template>
    <ul class="wizard" v-if="site">
        <router-link :to="{ name : 'site_repository', params : { site_id : site.id } }" tag="li" class="wizard-item">
            <a>Repository</a>
        </router-link>
        <template v-if="site">
            <router-link :to="{ name : 'site_files', params : { site_id : site.id } }" tag="li" class="wizard-item">
                <a>Files</a>
            </router-link>
        </template>
        <template v-if="site_servers">
            <router-link :to="{ name : 'site_workers', params : { site_id : site.id } }" tag="li" class="wizard-item">
                <a>Workers</a>
            </router-link>
            <router-link :to="{ name : 'site_ssl_certs', params : { site_id : site.id } }" tag="li" class="wizard-item">
                <a>SSLCertificates</a>
            </router-link>
        </template>
        <template v-if="site">
            <router-link :to="{ name : 'site_firewall_rules', params : { site_id : site.id } }" tag="li" class="wizard-item">
                <a>Site Firewall Rules</a>
            </router-link>
        </template>
        <template v-if="site.repository">
            <router-link :to="{ name : 'site_server_features', params : { site_id : site.id } }" tag="li" class="wizard-item">
                <a>Server Features</a>
            </router-link>
        </template>

    </ul>
</template>

<script>
    export default {
        computed: {
            site() {
                return this.$store.state.sitesStore.site;
            },
            site_servers() {
                return [];
            }
        }
    }
</script>
<template>
    <ul class="wizard" v-if="site">

        <router-link :to="{ name : 'site_repository', params : { site_id : site.id } }" tag="li" class="wizard-item">
            <a>Repository</a>
        </router-link>

        <router-link :to="{ name : 'site_deployment', params : { site_id : site.id } }" tag="li" class="wizard-item">
            <a>Deployment</a>
        </router-link>

        <router-link :to="{ name : 'site_files', params : { site_id : site.id } }" tag="li" class="wizard-item">
            <a>Files</a>
        </router-link>

        <router-link :to="{ name : 'site_workers', params : { site_id : site.id } }" tag="li" class="wizard-item">
            <a>Workers</a>
        </router-link>

        <router-link :to="{ name : 'site_ssl_certs', params : { site_id : site.id } }" tag="li" class="wizard-item" v-if="site.domain != 'default' ">
            <a>SSLCertificates</a>
        </router-link>

        <router-link :to="{ name : 'site_firewall_rules', params : { site_id : site.id } }" tag="li" class="wizard-item">
            <a>Site Firewall Rules</a>
        </router-link>

        <router-link :to="{ name : 'site_ssh_keys', params : { site_id : site.id } }" tag="li" class="wizard-item">
            <a>Site Ssh Keys</a>
        </router-link>

        <router-link :to="{ name : 'site_server_features', params : { site_id : site.id } }" tag="li" class="wizard-item" v-if="site.repository">
            <a>Server Features</a>
        </router-link>
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
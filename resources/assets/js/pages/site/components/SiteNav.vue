<template>
    <ul class="wizard" v-if="site">
        <router-link :to="{ path : '/site/' + site.id + '/repository'}" tag="li" class="wizard-item">
            <a>Repository</a>
        </router-link>
        <template v-if="site.framework">
            <router-link :to="{ path : '/site/' + site.id + '/framework-files'}" tag="li" class="wizard-item">
                <a>Framework Files</a>
            </router-link>
        </template>
        <template v-if="site_servers">
            <router-link :to="{ path : '/site/' + site.id + '/workers'}" tag="li" class="wizard-item">
                <a>Workers</a>
            </router-link>
            <router-link :to="{ path : '/site/' + site.id + '/ssl-certificates'}" tag="li" class="wizard-item">
                <a>SSLCertificates</a>
            </router-link>
        </template>
        <template v-if="site.repository">
            <router-link :to="{ path : '/site/' + site.id + '/server-features'}" tag="li" class="wizard-item">
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
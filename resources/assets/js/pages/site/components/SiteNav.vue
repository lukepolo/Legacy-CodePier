<template>
    <ul class="wizard" v-if="site">
        <router-link :to="{ name : 'site_repository', params : { site_id : site.id } }" tag="li" class="wizard-item">
            <a>Site Setup</a>
        </router-link>

        <router-link :to="{ name : 'site_ssh_keys', params : { site_id : site.id } }" tag="li" class="wizard-item">
            <a>Security</a>
        </router-link>

        <router-link :to="{ name : 'site_workers', params : { site_id : site.id } }" tag="li" class="wizard-item">
            <a>Server Setup</a>
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
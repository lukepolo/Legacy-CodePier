<template>
    <section>
        <ul id="tabs" class="nav nav-tabs" data-tabs="tabs" v-if="site">
            <router-link :to="{ path : '/site/' + site.id}" tag="li">
                <a class="btn btn-primary">Repository</a>
            </router-link>
            <template v-if="site.framework">
                <router-link :to="{ path : '/site/' + site.id + '/framework-files'}" tag="li">
                    <a class="btn btn-primary">Framework Files</a>
                </router-link>
            </template>
            <template v-if="site_servers">
                <router-link :to="{ path : '/site/' + site.id + '/workers'}" tag="li">
                    <a class="btn btn-primary">Workers</a>
                </router-link>
                <router-link :to="{ path : '/site/' + site.id + '/ssl-certificates'}" tag="li">
                    <a class="btn btn-primary">SSLCertificates</a>
                </router-link>
            </template>
            <template v-if="site.repository">
                <router-link :to="{ path : '/site/' + site.id + '/server-features'}" tag="li">
                    <a class="btn btn-primary">Server Features</a>
                </router-link>
            </template>
        </ul>
    </section>
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
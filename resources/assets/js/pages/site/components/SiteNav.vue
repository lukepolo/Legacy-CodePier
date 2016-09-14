<template>
    <section>
        <ul id="tabs" class="nav nav-tabs" data-tabs="tabs" v-if="site">
            <router-link :to="{ path : '/site/' + site.id}" tag="li" class="btn btn-primary">Repository</router-link>
            <template v-if="site.framework">
                <router-link :to="{ path : '/site/' + site.id + '/framework-files'}" tag="li" class="btn btn-primary">Framework Files</router-link>
            </template>
            <template v-if="site.servers.length">
                <router-link :to="{ path : '/site/' + site.id + '/workers'}" tag="li" class="btn btn-primary">Workers</router-link>
                <router-link :to="{ path : '/site/' + site.id + '/ssl-certificates'}" tag="li" class="btn btn-primary">SSLCertificates</router-link>
            </template>
            <template v-if="site.repository">
                <router-link :to="{ path : '/site/' + site.id + '/server-features'}" tag="li" class="btn btn-primary">Server Features</router-link>
            </template>
        </ul>
    </section>
</template>

<script>
    export default {
        computed : {
            site : () => {
                return siteStore.state.site;
            }
        }
    }
</script>
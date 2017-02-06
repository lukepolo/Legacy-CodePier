<template>
    <section>
        <table class="table">
            <thead>
            <tr>
                <th>Domain</th>
                <th>Repository</th>
                <th>ZeroTime Deployment</th>
                <th># of Workers</th>
                <th>WildCard Domain</th>
                <th>SSL</th>
            </tr>
            </thead>
            <tbody>
            <tr v-if="sites" v-for="site in sites">
                <td>
                    <router-link :to="{ name: 'site_repository', params : { site_id : site.id} }">
                        {{ site.name }}
                    </router-link>
                </td>
                <td>{{ site.repository }}</td>
                <td class="text-center">
                    <span v-if="isZerotimeDeployment(site)">
                        Yes
                    </span>
                    <span v-else>
                        No
                    </span>
                </td>
                <td class="text-center">{{ site.workers }}</td>
                <td class="text-center">{{ site.wildcard_domain }}</td>
                <td class="text-center">
                    <span v-if="hasActiveSSL(site)">
                        Yes
                    </span>
                    <span v-else>
                        No
                    </span>
                </td>
            </tr>
            </tbody>
        </table>
    </section>
</template>

<script>
    export default {
        created() {
            this.fetchData();
        },
        watch: {
            '$route': 'fetchData'
        },
        methods: {
            fetchData() {
                this.$store.dispatch('getServerSites', this.$route.params.server_id);
            },
            isZerotimeDeployment(site) {
                if (site.zerotime_deployment) {
                    return true;
                }
                return false;
            },
            hasActiveSSL(site) {
                if (site.activeSsl) {
                    return true;
                }
                return false;
            }
        },
        computed: {
            server() {
                return this.$store.state.serversStore.server;
            },
            sites() {
                return this.$store.state.serversStore.server_sites;
            }
        }
    }
</script>

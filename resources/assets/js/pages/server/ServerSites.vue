<template>
    <section>
        <left-nav></left-nav>
        <section id="middle" class="section-column"  v-if="server">
            <server-nav :server="server"></server-nav>
            <table class="table" v-if="sites.length" v-for="site in sites">
                <thead>
                <tr>
                    <th>Domain</th>
                    <th>Repository</th>
                    <th>ZeroTime Deployment</th>
                    <th>Workers</th>
                    <th>WildCard Domain</th>
                    <th>SSL</th>
                </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <router-link :to="{ path: '/site/'+site.id }">
                                {{ site.name }}
                            </router-link>
                        </td>
                        <td>{{ site.repository }}</td>
                        <td>
                             <span v-if="isZerotimeDeployment(site)">
                                Yes
                            </span>
                            <span v-else>
                                No
                            </span>
                        </td>
                        <td>{{ site.workers.length }}</td>
                        <td>{{ site.wildcard_domain }}</td>
                        <td>
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
    </section>
</template>

<script>
    import ServerNav from './components/ServerNav.vue';
    import LeftNav from './../../core/LeftNav.vue';

    export default {
        components : {
            LeftNav,
            ServerNav
        },
        created() {
            this.fetchData();
        },
        watch: {
            '$route': 'fetchData'
        },
        methods: {
            fetchData: function () {
                this.$store.dispatch('getServer', this.$route.params.server_id);
                this.$store.dispatch('getServerSites', this.$route.params.server_id);
            },
            isZerotimeDeployment(site) {
              if(site.zerotime_deployment) {
                  return true;
              }
              return false;
            },
            hasActiveSSL(site) {
                if(site.activeSSL) {
                    return true;
                }
                return false;
            }
        },
        computed : {
            server : () => {
                return serverStore.state.server;
            },
            sites : () => {
                return serverStore.state.server_sites;
            }
        }
    }
</script>

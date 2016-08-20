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
                        <td><a href="#">{{ site.domain }}</a></td>
                        <td>{{ site.repository }}</td>
                        <td>
                             <span v-if="isZerotimeDeployment(site)">
                                Yes
                            </span>
                            <span v-else>
                                No
                            </span>
                        </td>
                        <td>{{ site.daemons.length }}</td>
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
        <!--<a href="#" class="btn btn-xs">Archive Server</a>-->
        <!--<a href="#" class="btn btn-xs">Restart Web Services</a>-->
        <!--<a href="#" class="btn btn-xs">Restart Server</a>-->
        <!--<a href="#" class="btn btn-xs">Restart Database</a>-->
        <!--<a href="#" class="btn btn-xs">Restart Workers</a>-->
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
        data() {
            return {
                server : null,
                sites : []
            }
        },
        created() {
            this.fetchData();
        },
        watch: {
            '$route': 'fetchData'
        },
        methods: {
            fetchData: function () {
                Vue.http.get(this.action('Server\ServerController@show', {server : this.$route.params.server_id})).then((response) => {
                    this.server = response.json();
                }, (errors) => {
                    alert(error);
                });

                Vue.http.get(this.action('Server\ServerSiteController@show', {server : this.$route.params.server_id})).then((response) => {
                    this.sites = response.json();
                }, (errors) => {
                    alert(error);
                });
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
        }
    }
</script>

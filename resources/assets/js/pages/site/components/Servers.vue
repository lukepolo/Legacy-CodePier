<template>
    <section id="right" v-if="site">
        <template v-for="server in servers">
            <router-link :to="{ path: '/server/'+server.id+'/sites' }">
                {{ server.name }}
            </router-link>
            {{ server.ssh_connection }} - {{ server.name }} - {{ server.ip }}
            <p>4 / 40 GB</p>
            <p>1.2 Avg Load</p>
            <div class="dropdown">
                <button class="btn btn-default btn-xs dropdown-toggle" type="button" data-toggle="dropdown">
                    Actions
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                    <li><a href="#">Restart Web Services</a></li>
                    <li><a href="#">Restart Server</a></li>
                    <li><a href="#">Restart Database</a></li>
                    <li><a href="#">Restart Workers</a></li>
                    <li role="separator" class="divider"></li>
                    <li><a href="#">Archive Server</a></li>
                </ul>
            </div>
        </template>

        <hr>
        <template v-if ="availableServers.length">
            Available Servers
            <form @submit.prevent="linkServers">
                <template v-for="server in availableServers">
                    <input type="checkbox" :value="server.id" v-model="form.connected_servers"> {{ server.ssh_connection }} - {{ server.name }} - {{ server.ip }}
                    <br>
                </template>
                <button type="submit">Link Servers</button>
            </form>
        </template>

        <section v-if="site.server_features">
            <div class="btn btn-primary">Create A Full Stack Server</div>
            <hr>
            <div class="btn btn-primary">Create A Web Server</div> - not available during beta
            <div class="btn btn-primary">Create A Load Balance</div> - not available during beta
            <div class="btn btn-primary">Create A Database Server</div> - not available during beta
            <div class="btn btn-primary">Create A Queue Worker Serer</div> - not available during beta
        </section>
    </section>
</template>

<script>
    export default {
        data()  {
            return {
                form : {
                    connected_servers : [],
                    site: this.$route.params.site_id
                }
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
                siteStore.dispatch('getSiteServers', this.$route.params.site_id);
                serverStore.dispatch('getServers');
            },
            linkServers : function() {
                siteStore.dispatch('updateLinkedServers', this.form);
            }
        },
        computed : {
            site : () => {
                return siteStore.state.site;
            },
            servers : () => {
                return siteStore.state.site_servers;
            },
            availableServers : () => {
                return serverStore.state.servers;
            }
        }
    }
</script>
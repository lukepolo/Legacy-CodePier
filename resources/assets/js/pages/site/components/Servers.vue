<template>
    <section id="right" v-if="site" class="section-column">
        <h3 class="section-header">
            Server Info
        </h3>

        <div class="section-content">
        <template v-for="server in servers">
            <server-info :server="server"></server-info>
        </template>

        <template v-if="availableServers.length">
            Available Servers
            <form @submit.prevent="linkServers">
                <template v-for="server in availableServers">
                    <input
                        type="checkbox"
                        :value="server.id"
                        v-model="form.connected_servers"
                    >
                    {{ server.ssh_connection}} - {{ server.name }} - {{ server.ip }}
                    <br>
                </template>
                <button class="btn btn-primary" type="submit">Link Servers</button>
            </form>
        </template>

        <hr>
        <div v-if="site.server_features">
            <router-link :to="{ name : 'server_form' , params : { site : site.id , type : 'full_stack' } }">
                <a class="btn btn-primary">Create A Full Stack Server</a>
            </router-link>
            <!--<div class="btn btn-primary">Create A Web Server</div>-->
            <!-- - not available during beta-->
            <!--<div class="btn btn-primary">Create A Load Balance</div>-->
            <!-- - not available during beta-->
            <!--<div class="btn btn-primary">Create A Database Server</div>-->
            <!-- - not available during beta-->
            <!--<div class="btn btn-primary">Create A Queue Worker Serer</div>-->
            <!-- - not available during beta-->
        </div>

        </div>
    </section>
</template>

<script>
    import ServerInfo from './ServerInfo.vue';
    export default {
        components : {
            ServerInfo
        },
        data()  {
            return {
                form: {
                    connected_servers: [],
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
            fetchData() {
                this.$store.dispatch('getSiteServers', this.$route.params.site_id);
                this.$store.dispatch('getServers');
            },
            linkServers() {
                this.$store.dispatch('updateLinkedServers', this.form);
            }
        },
        computed: {
            site() {
                return this.$store.state.sitesStore.site;
            },
            servers() {
                return this.$store.state.sitesStore.site_servers;
            },
            availableServers() {
                return this.$store.state.serversStore.servers;
            }
        }
    }
</script>
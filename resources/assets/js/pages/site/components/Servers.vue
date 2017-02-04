<template>
    <section id="right" v-if="site" class="section-column">

        <h3 class="section-header">Server Info</h3>

        <div class="section-content">

            <template v-for="server in servers">
                <server-info :server="server" :showInfo="servers.length == 1 ? true : false"></server-info>
            </template>

            <template v-if="availableServers.length">

                <h3 class="section-header secondary">Available Servers</h3>

                <form @submit.prevent="linkServers">

                    <div class="jcf-form-wrap">

                        <template v-for="server in availableServers">
                            <form class="floating-labels">
                                <div class="jcf-input-group input-checkbox">
                                    <label>
                                        <input
                                            type="checkbox"
                                            :value="server.id"
                                            v-model="form.connected_servers"
                                        >
                                        <span class="icon"></span>
                                        {{ server.name }} ({{ server.ip }})
                                    </label>
                                </div>
                            </form>
                        </template>

                        <div class="btn-footer">
                            <button class="btn" type="submit" :disabled="hasSelectedServers">Attach to servers</button>
                        </div>

                    </div>

                </form>

            </template>

            <div class="btn-footer">
                <template v-if="site.repository">
                    <router-link :to="{ name : 'server_form_with_site' , params : { site : site.id , type : 'full_stack' } }">
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
                </template>
                <template v-else>
                    <h3>Alpha Testing : </h3>
                    <p>
                        Please enter your repository details to continue.
                    </p>
                </template>
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
            },

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
            },
            hasSelectedServers() {
                return !(this.form.connected_servers > 0)
            }
        }
    }
</script>
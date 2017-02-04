<template>
    <section id="right" v-if="site" class="section-column">

        <h3 class="section-header">
            Server Info

            <div class="pull-right">

                <div class="dropdown">

                    <button class="btn btn-default btn-xs dropdown-toggle" type="button" data-toggle="dropdown">
                        <span class="fa fa-plus"></span>
                    </button>
                    <ul class="dropdown-menu nowrap">
                        <template v-if="site.repository">
                            <li>
                                <router-link :to="{ name : 'server_form_with_site' , params : { site : site.id , type : 'full_stack' } }">
                                    <span class="icon-server"></span> Create A Full Stack Server
                                </router-link>
                            </li>
                            <!--<div class="btn btn-primary">Create A Web Server</div>-->
                            <!-- - not available during beta-->
                            <!--<div class="btn btn-primary">Create A Load Balance</div>-->
                            <!-- - not available during beta-->
                            <!--<div class="btn btn-primary">Create A Database Server</div>-->
                            <!-- - not available during beta-->
                            <!--<div class="btn btn-primary">Create A Queue Worker Serer</div>-->
                            <!-- - not available during beta-->
                            <li role="separator" class="divider"></li>
                            <template v-if="availableServers.length">
                                <li>
                                    <a href="#" @click.prevent="connectServers = !connectServers">
                                        <span class="icon-server"></span> Attach to servers
                                    </a>
                                </li>
                            </template>

                        </template>
                        <template v-else>
                            <li>
                                <router-link :to="{ name : 'site_repository' , params : { site : site.id } }">
                                    <span class="icon-site"></span> Enter repository information in first.
                                </router-link>
                            </li>
                        </template>
                    </ul>
                </div>
            </div>
        </h3>

        <div class="section-content">

            <template v-if="!connectServers">
                <template v-for="server in servers">
                    <server-info :server="server" :showInfo="servers.length == 1 ? true : false"></server-info>
                </template>
            </template>
           <template v-else>

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
                               <button class="btn" type="submit" :disabled="hasSelectedServers">{{ attachServersText }}</button>
                               <button class="btn danger" @click.prevent="connectServers = !connectServers">Cancel</button>
                           </div>

                       </div>

                   </form>

               </template>
           </template>
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
                connectServers : false,
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
                return !(this.form.connected_servers.length > 0)
            },
            attachServersText() {
                return 'Attach to ' + _('server').pluralize(this.form.connected_servers.length)
            }

        }
    }
</script>
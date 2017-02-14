<template>
    <section id="right" v-if="site && site.repository" class="section-column">

        <h3 class="section-header">
            Server Info

            <div class="pull-right">

                <div class="dropdown">

                    <button class="btn btn-default btn-xs dropdown-toggle" type="button" data-toggle="dropdown">
                        <span class="fa fa-plus"></span>
                    </button>
                    <ul class="dropdown-menu nowrap">
                        <server-create-list></server-create-list>
                        <template v-if="availableServers.length">
                            <li>
                                <a href="#" @click.prevent="connectServers = !connectServers">
                                    <span class="icon-server"></span> Attached Servers
                                </a>
                            </li>
                        </template>
                    </ul>
                </div>
            </div>
        </h3>

        <div class="section-content">

            <template v-if="!connectServers && siteServers.length">
                <template v-for="server in siteServers">
                    <server-info :server="server" :showInfo="siteServers.length == 1 ? true : false"></server-info>
                </template>
            </template>
           <template v-else>

               <template v-if="availableServers.length">

                   <h3 class="section-header--secondary">Available Servers</h3>

                   <form @submit.prevent="linkServers">

                       <div class="jcf-form-wrap">
                           <div class="jcf-input-group">
                               <div class="small">
                                   Select the servers you want to attach your site to.
                               </div>
                           </div>

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
                               <template v-if="siteServers.length">
                                   <button class="btn danger" @click.prevent="resetAttachedServers">Cancel</button>
                               </template>
                               <!-- todo - disable attach button when no servers are selected -->
                               <button class="btn btn-primary" type="submit">{{ attachServersText }}</button>
                           </div>

                       </div>

                   </form>

               </template>

               <template v-else>
                   <h3 class="section-header--secondary">Lets create your first Server</h3>
                   <ul style="list-style: none; padding-left: 2em;">
                       <server-create-list classes="btn"></server-create-list>
                   </ul>
               </template>

           </template>
        </div>
    </section>
</template>

<script>
    import ServerInfo from './ServerInfo.vue';
    import ServerCreateList from './ServerCreateList.vue'
    export default {
        components : {
            ServerInfo,
            ServerCreateList
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
                this.connectServers = false
                this.form.connected_servers = []
                this.$store.dispatch('getServers');
                this.$store.dispatch('getSiteServers', this.$route.params.site_id);
            },
            linkServers() {
                this.$store.dispatch('updateLinkedServers', this.form).then(() => {
                    this.connectServers = false
                })
            },
            resetAttachedServers() {
                this.connectServers = false
                this.form.connected_servers = _.map(this.siteServers, 'id')
            }
        },
        computed: {
            site() {
                return this.$store.state.sitesStore.site;
            },
            siteServers() {
                let siteServers = this.$store.state.sitesStore.site_servers;
                this.form.connected_servers = _.map(siteServers, 'id')
                return siteServers;
            },
            availableServers() {
                return this.$store.state.serversStore.servers;
            },
            attachServersText() {
                return 'Attach ' + _('server').pluralize(this.form.connected_servers.length)
            }

        }
    }
</script>
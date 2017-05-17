<template>
    <section id="right" v-if="site" class="section-column">

        <h3 class="section-header">
            Servers

            <div class="section-header--btn-right">
                <drop-down icon="fa fa-plus" class="btn btn-default btn-xs">
                    <server-create-list></server-create-list>
                    <template v-if="availableServers.length">
                        <li>
                            <a href="#" @click.prevent="connectServers = !connectServers">
                                <span class="icon-server"></span> Attached Servers
                            </a>
                        </li>
                    </template>
                </drop-down>
            </div>
        </h3>

        <div class="section-content">

            <template v-if="!connectServers && siteServers">
                <template v-for="server in siteServers">
                    <server-info :server="server" :showInfo="showInfo"></server-info>
                </template>
            </template>
           <template v-else>

               <template v-if="site.repository">
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
                                   <template v-if="siteServers">
                                       <button class="btn danger" @click.prevent="resetAttachedServers">Cancel</button>
                                   </template>
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

               <template v-else>
                   <div class="jcf-form-wrap">
                       <div class="jcf-input-group">
                           <h5 class="section-header--secondary">
                               Please fill out your <br>repository information before creating a server
                           </h5>
                       </div>
                   </div>
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
                    connected_servers: []
                },
                showInfo : false
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
                this.$store.dispatch('user_servers/get');
            },
            linkServers() {
                this.form.site = this.$route.params.site_id
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
                return this.$store.state.user_sites.site;
            },
            siteServers() {
                let servers = this.$store.getters['user_site_servers/getServers'](this.$route.params.site_id)

                if(servers && servers.length == 1) {
                    this.showInfo = true
                }

                this.form.connected_servers = _.map(servers, 'id')

                return servers
            },
            availableServers() {
                return _.filter(this.$store.state.user_servers.servers, function(server){
                    if(server.progress >= 100) {
                        return true
                    }
                });
            },
            attachServersText() {
                let serverCount = this.form.connected_servers.length
                return 'Attach ' + _('server').pluralize(serverCount > 0 ? serverCount : 1)
            }

        }
    }
</script>
<template>
    <div v-if="site">
        <h3 class="section-header">
            Attached Servers

            <div class="section-header--btn-right" v-if="siteServers && siteServers.length || availableServers.length">
                <drop-down icon="fa fa-plus" class="btn btn-default btn-xs" :class="{ 'btn-disabled' : !serverCreateEnabled }">
                    <server-create-list></server-create-list>
                    <template v-if="availableServers.length">
                        <li>
                            <a href="#" @click.prevent="connectServers = !connectServers">
                                <span class="icon-server"></span> Attach a provisioned server
                            </a>
                        </li>
                    </template>
                </drop-down>
            </div>

            <span class="icon-server"></span>
        </h3>

        <div class="section-content">

            <template v-if="!connectServers && siteServers">
                <template v-for="server in siteServers">
                    <server-info :server="server" :showInfo="siteServers.length < 2" :key="server.id"></server-info>
                </template>

                <template v-if="!isSubscribed">
                    <div class="slack-invite">
                        <router-link :to="{ name : 'subscription' }">
                            Upgrade Account
                            <div class="small">The free plan only allows for 1 server. <br>Upgrade now to add more!</div>
                        </router-link>
                    </div>
                </template>
                <template v-else-if="!serverCreateEnabled">
                    <div class="slack-invite">
                        <router-link :to="{ name : 'subscription' }">
                            Upgrade Account
                            <div class="small">Your current plan only allows for 30 servers. <br>Upgrade now to add more!</div>
                        </router-link>
                    </div>
                </template>
            </template>
           <template v-else>

               <template v-if="site.repository && workFlowCompleted">
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
                                       <span class="btn danger" @click.prevent="resetAttachedServers">Cancel</span>
                                   </template>
                                   <button class="btn btn-primary" type="submit">{{ attachServersText }}</button>
                               </div>

                           </div>

                       </form>

                   </template>

                   <template v-else>
                       <h3 class="section-header--secondary">Lets create your first Server</h3>
                       <server-create-list classes="btn"></server-create-list>
                       <template v-if="availableServers.length">
                           <li>
                               <a href="#" @click.prevent="connectServers = !connectServers">
                                   <span class="icon-server"></span> Attached Servers
                               </a>
                           </li>
                       </template>
                   </template>
               </template>

               <template v-else>
                   <div class="jcf-form-wrap">
                       <div class="jcf-input-group">
                           <h5 class="section-header--secondary">
                               Please fill out your <br>app requirements before creating a server
                           </h5>
                       </div>
                   </div>
               </template>

           </template>
        </div>
    </div>
</template>

<script>
import ServerInfo from "./ServerInfo";
import ServerCreateList from "./ServerCreateList";

export default {
  components: {
    ServerInfo,
    ServerCreateList
  },
  data() {
    return {
      connectServers: false,
      form: this.createForm({
        connected_servers: []
      })
    };
  },
  created() {
    this.fetchData();
  },
  watch: {
    $route: "fetchData",
    siteServers: function() {
      this.resetAttachedServers();
    }
  },
  methods: {
    fetchData() {
      this.connectServers = false;
      this.form.connected_servers = [];
    },
    linkServers() {
      this.form.site = this.$route.params.site_id;
      this.$store
        .dispatch("user_site_servers/updateLinks", this.form)
        .then(() => {
          this.connectServers = false;
        });
    },
    resetAttachedServers() {
      this.connectServers = false;
      this.form.connected_servers = _.map(this.siteServers, "id");
    }
  },
  computed: {
    user() {
      return this.$store.state.user.user;
    },
    site() {
      return this.$store.state.user_sites.site;
    },
    siteServers() {
      return this.$store.getters["user_site_servers/getServers"](
        this.$route.params.site_id
      );
    },
    availableServers() {
      return _.filter(this.$store.state.user_servers.servers, server => {
        if (server.progress >= 100) {
          return true;
        }
      });
    },
    attachServersText() {
      let serverCount = this.form.connected_servers.length;

      if (this.siteServers && serverCount < this.siteServers.length) {
        return "Update Attached";
      }

      return (
        "Attach " + _("server").pluralize(serverCount > 0 ? serverCount : 1)
      );
    }
  }
};
</script>

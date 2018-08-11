<template>
    <h3 class="section-header primary" v-if="site">

        <router-link :to="{ name : 'site_overview', params : { site : site.id } }">
            {{ site.name }}
        </router-link>

        <template v-if="site.domain !== 'default'">
            &nbsp; <a :href="'//'+site.domain" target="_blank"><i class="fa fa-external-link"></i></a>
        </template>

        <div class="section-header--btn-right">

            <drop-down tag="span" v-if="siteServers">

                <button slot="header" class="btn btn-default btn-xs dropdown-toggle" type="button" data-toggle="dropdown">
                    <span class="icon-refresh2"></span>
                </button>

                <ul slot="content" class="dropdown-menu nowrap dropdown-list">
                    <li>
                        <confirm-dropdown dispatch="user_site_services/restartWebServices" :params="site.id"><a @click.prevent href="#"><span class="icon-web"></span> Restart Web Services</a></confirm-dropdown>
                    </li>
                    <li>
                        <confirm-dropdown dispatch="user_site_services/restartServers" :params="site.id"><a @click.prevent href="#"><span class="icon-server"></span> Restart Servers</a></confirm-dropdown>
                    </li>
                    <li>
                        <confirm-dropdown dispatch="user_site_services/restartDatabases" :params="site.id"><a @click.prevent href="#"><span class="icon-database"></span> Restart Databases</a></confirm-dropdown>
                    </li>
                    <li>
                        <confirm-dropdown dispatch="user_site_services/restartWorkers" :params="site.id"><a @click.prevent href="#"><span class="icon-worker"></span> Restart Workers & Daemons</a></confirm-dropdown>
                    </li>
                </ul>

            </drop-down>


            <drop-down tag="span" v-if="siteServers">

                <button slot="header" class="btn btn-default btn-xs dropdown-toggle" type="button" data-toggle="dropdown">
                    <span class="fa fa-life-buoy"></span>
                </button>

                <ul slot="content" class="dropdown-menu nowrap dropdown-list">
                    <li>
                        <confirm-dropdown dispatch="user_sites/fixServerConfigurations" :params="site.id"><a href="#"><span class="fa fa-medkit"></span> Fix Server Configurations</a></confirm-dropdown>
                    </li>
                    <li>
                        <confirm-dropdown dispatch="user_site_server_commands/clearStuckCommands" :params="site.id"><a href="#"><span class="fa fa-bomb"></span> Clear Stuck Commands</a></confirm-dropdown>
                    </li>
                </ul>

            </drop-down>

        </div>


    </h3>
</template>

<script>
export default {
  computed: {
    site() {
      return this.$store.state.user_sites.site;
    },
    siteServers() {
      let siteServers = _.get(
        this.$store.state.user_site_servers.servers,
        this.$route.params.site_id,
      );

      if (siteServers && siteServers.length) {
        return siteServers;
      }
    },
  },
};
</script>

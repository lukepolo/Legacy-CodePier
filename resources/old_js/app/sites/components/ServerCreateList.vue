<template>
    <div class="server-type-list">

        <p>Select the type of server you need.</p>

        <ul>
            <li v-for="(serverType, serverTypeText) in serverTypes">
                <template v-if="site.repository">
                    <router-link
                        :to="{
                            name : 'server_form_with_site' ,
                            params : {
                                site_id : site.id ,
                                type : serverType,
                                disabled : !serverTypesEnabled && serverType !== 'full_stack'
                            },
                        }"
                        :class="{disabled : !serverTypesEnabled && serverType !== 'full_stack'}"
                    >
                        {{ serverTypeText }} Server
                    </router-link>
                    <template v-if="serverType === 'full_stack' && !serverTypesEnabled">
                        <div class="slack-invite">
                            <router-link :to="{ name : 'subscription' }" class="server-type-list-text">
                                Upgrade Account
                                <div class="small">Upgrade now to create the following server types:</div>
                            </router-link>
                        </div>
                    </template>
                </template>
            </li>
        </ul>
    </div>
</template>

<script>
export default {
  props: {
    classes: {
      default: "",
    },
  },
  computed: {
    site() {
      return this.$store.state.user_sites.site;
    },
    serverTypes() {
      return _.pickBy(window.Laravel.serverTypes, (type) => {
        if (this.hasLoadBalancer && type === "load_balancer") {
          return false;
        }

        return true;
      });
    },
    siteServers() {
      return this.$store.getters["user_site_servers/getServers"](
        this.$route.params.site_id,
      );
    },
    servers() {
      return this.$store.state.user_servers.servers;
    },
    hasLoadBalancer() {
      return (
        _.filter(this.siteServers, function(server) {
          return server.type === "load_balancer";
        }).length > 0
      );
    },
  },
};
</script>

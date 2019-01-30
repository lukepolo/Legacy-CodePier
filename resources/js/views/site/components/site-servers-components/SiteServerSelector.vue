<template>
  <div class="server-type-list">
    <p>Select the type of server you need.</p>
    <ul>
      <li v-for="(serverTypeText, serverType) in serverTypes">
        <router-link
          :to="serverTypeLink(serverType)"
          :class="{
            disabled: !serverTypesEnabled && serverType !== 'full_stack',
          }"
        >
          {{ serverTypeText }}
        </router-link>
        <template v-if="serverType === 'full_stack' && !serverTypesEnabled">
          <div class="slack-invite">
            <router-link
              :to="{ name: 'my.subscription' }"
              class="server-type-list-text"
            >
              Upgrade Account
              <div class="small">Upgrade to create other server types.</div>
            </router-link>
          </div>
        </template>
      </li>
    </ul>
  </div>
</template>

<script>
import serverTypes from "@app/constants/serverTypes";
export default {
  methods: {
    serverTypeLink(serverType) {
      return {
        name: "server.create",
        params: {
          type: serverType,
          site: this.site.id,
          disabled:
            !this.serverTypesEnabled &&
            serverTypes[serverType] !== serverTypes.full_stack,
        },
      };
    },
  },
  computed: {
    serverTypes() {
      return serverTypes;
    },
    siteServers() {
      return [];
    },
    servers() {
      return [];
    },
    site() {
      return this.$store.getters["user/sites/show"](this.$route.params.site);
    },
    hasLoadBalancer() {
      // TODO
      return false;
      // return (
      //   _.filter(this.siteServers, function(server) {
      //     return server.type === "load_balancer";
      //   }).length > 0
      // );
    },
  },
};
</script>

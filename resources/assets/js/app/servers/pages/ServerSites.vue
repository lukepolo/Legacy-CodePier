<template>
    <section>
        <table class="table">
            <thead>
                <tr>
                    <th>Domain</th>
                    <th>Repository</th>
                    <th>Zero Downtime Deployment</th>
                    <th># of Workers</th>
                    <th>WildCard Domain</th>
                    <th>SSL</th>
                </tr>
            </thead>
            <tbody>
                <tr v-if="sites" v-for="site in sites">
                    <td>
                        <router-link :to="{ name: 'site_overview', params : { site_id : site.id} }">
                            {{ site.name }}
                        </router-link>
                    </td>
                    <td>{{ site.repository }}</td>
                    <td class="text-center">
                        <span v-if="iszeroDowntimeDeployment(site)">
                            Yes
                        </span>
                        <span v-else>
                            No
                        </span>
                    </td>
                    <td class="text-center">{{ site.workers }}</td>
                    <td class="text-center">{{ site.wildcard_domain }}</td>
                    <td class="text-center">
                        <span v-if="hasActiveSSL(site)">
                            Yes
                        </span>
                        <span v-else>
                            No
                        </span>
                    </td>
                </tr>
            </tbody>
        </table>
    </section>
</template>

<script>
export default {
  created() {
    this.fetchData();
  },
  watch: {
    $route: "fetchData"
  },
  methods: {
    fetchData() {
      this.$store.dispatch(
        "user_server_sites/get",
        this.$route.params.server_id
      );
    },
    iszeroDowntimeDeployment(site) {
      if (site.zero_downtime_deployment) {
        return true;
      }
      return false;
    },
    hasActiveSSL(site) {
      if (site.activeSsl) {
        return true;
      }
      return false;
    }
  },
  computed: {
    server() {
      return this.$store.state.user_servers.server;
    },
    sites() {
      return this.$store.state.user_server_sites.sites;
    }
  }
};
</script>

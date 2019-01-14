<template>
  <section>
    <template v-if="databases && databases.length">
      <database-section
        :database="database"
        v-for="database in databases"
        :key="database.id"
      ></database-section>
    </template>
    <template v-else>
      <p>Currently you do not have any databases installed on this system.</p>
    </template>
  </section>
</template>

<script>
import DatabaseSection from "./components/DatabaseSection";
export default {
  components: {
    DatabaseSection,
  },
  created() {
    this.fetchData();
  },
  watch: {
    $route: "fetchData",
  },
  methods: {
    fetchData() {
      this.$store.dispatch("user/sites/schemas/get", { site: this.siteId });
      this.$store.dispatch("user/sites/schema/users/get", {
        site: this.siteId,
      });
      this.$store.dispatch("user/sites/servers/features/get", {
        site: this.siteId,
      });
    },
  },
  computed: {
    siteId() {
      return this.$route.params.site;
    },
    databases() {
      let serverFeatures = this.$store.state.user.sites.servers.features
        .features;

      console.info(serverFeatures);

      // if (_.has(serverFeatures, "DatabaseService")) {
      //   return _.keys(
      //     _.pickBy(serverFeatures.DatabaseService, function(options, database) {
      //       if (
      //         database === "MariaDB" ||
      //         database === "PostgreSQL" ||
      //         database === "MySQL" ||
      //         database === "MongoDB"
      //       ) {
      //         return options.enabled;
      //       }
      //     })
      //   );
      // }
    },
  },
};
</script>

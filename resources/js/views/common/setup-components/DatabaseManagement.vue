<template>
  <section>
    <template v-if="databases.length > 0">
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
  watch: {
    $route: {
      immediate: true,
      handler() {
        this.$store.dispatch("user/sites/schemas/get", { site: this.siteId });
        this.$store.dispatch("user/sites/schema/users/get", {
          site: this.siteId,
        });
        this.$store.dispatch("user/sites/servers/features/get", {
          site: this.siteId,
        });
      },
    },
  },
  computed: {
    siteId() {
      return this.$route.params.site;
    },
    databases() {
      let databases = [];
      let serverFeatures = this.$store.state.user.sites.servers.features
        .features;

      if (serverFeatures && serverFeatures.hasOwnProperty("DatabaseService")) {
        for (let feature in serverFeatures.DatabaseService) {
          if (
            serverFeatures.DatabaseService[feature].enabled &&
            (feature === "MariaDB" ||
              feature === "PostgreSQL" ||
              feature === "MySQL" ||
              feature === "MongoDB")
          ) {
            databases.push(feature);
          }
        }
      }
      return databases;
    },
  },
};
</script>

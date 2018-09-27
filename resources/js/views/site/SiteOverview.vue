<template>
    <div v-if="site">
        <div class="flex">
            <div class="flex--grow">
                <h2 class="heading">Site Overview
                </h2>
            </div>
            <div class="heading--btns">
                <site-header :site="site"/>
            </div>
        </div>

        <div class="grid-2">
            <div class="grid--item">
                <h3>Repository @ Deploy Branch</h3>
                {{ site.repository }} @ {{ site.branch }}
            </div>
            <div class="grid--item">
                <site-dns :site="site"></site-dns>
            </div>
        </div>

        <div class="providers grid-3">
            <automatic-deployment-card :site="site"></automatic-deployment-card>
            <site-ssh-key-card :site="site"></site-ssh-key-card>
            <deployment-hook-card :site="site"></deployment-hook-card>
            <database-backup-card :site="site"></database-backup-card>
        </div>

        <div class="providers grid-2">
            <recent-deployments :site="site"></recent-deployments>
            <life-lines :site="site"></life-lines>
        </div>
    </div>
</template>

<script>
import SiteDns from "./components/dashboard-components/SiteDns";
import LifeLines from "./components/dashboard-components/Lifelines";
import SiteHeader from "./components/dashboard-components/SiteHeader";
import SiteSshKeyCard from "./components/dashboard-components/SiteSshKeyCard";
import RecentDeployments from "./components/dashboard-components/RecentDeployments";
import DatabaseBackupCard from "./components/dashboard-components/DatabaseBackupCard";
import DeploymentHookCard from "./components/dashboard-components/DeploymentHookCard";
import AutomaticDeploymentCard from "./components/dashboard-components/AutomaticDeploymentCard";

export default {
  components: {
    SiteDns,
    LifeLines,
    SiteHeader,
    SiteSshKeyCard,
    RecentDeployments,
    DatabaseBackupCard,
    DeploymentHookCard,
    AutomaticDeploymentCard,
  },
  created() {
    this.fetchData();
  },
  methods: {
    fetchData() {
      this.$store.dispatch("user/notification/provider/get");
      // this.$store.dispatch(
      //   "user_site_deployments/get",
      //   this.$route.params.site_id,
      // );
    },
  },
  computed: {
    site() {
      return this.$store.getters["user/sites/show"](this.$route.params.site);
    },
  },
};
</script>

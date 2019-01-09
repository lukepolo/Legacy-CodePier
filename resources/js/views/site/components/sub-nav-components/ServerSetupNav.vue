<template>
  <div class="tab-container tab-left">
    <ul class="nav nav-tabs">
      <router-link
        :to="{
          name: 'site.environment-variables',
          params: { site: siteId },
        }"
        tag="li"
        exact
      >
        <a>
          Environment Variables
          <div class="small">Add environment variables for your site</div>
        </a>
      </router-link>

      <router-link
        :to="{ name: 'site.cron-jobs', params: { site: siteId } }"
        tag="li"
        exact
      >
        <a>
          Cron Jobs
          <div class="small">Configure cron jobs</div>
        </a>
      </router-link>

      <router-link
        :to="{ name: 'site.daemons', params: { site: siteId } }"
        tag="li"
        exact
      >
        <a>
          Daemons
          <div class="small">Configure daemons processes</div>
          <div class="small" v-if="!canCreateWorkers">
            <span class="text-error"
              ><i class="fa fa-exclamation-triangle"></i> Your site is not
              configured to handle daemons.</span
            >
          </div>
        </a>
      </router-link>

      <router-link
        :to="{ name: 'site.workers', params: { site: siteId } }"
        tag="li"
        exact
      >
        <a>
          Workers
          <div class="small">Configure workers processes</div>
          <div class="small" v-if="!canCreateWorkers">
            <span class="text-error"
              ><i class="fa fa-exclamation-triangle"></i> Your site is not
              configured to handle workers.</span
            >
          </div>
        </a>
      </router-link>

      <router-link
        :to="{ name: 'site.server-files', params: { site: siteId } }"
        tag="li"
        v-if="siteServers && siteServers.length >= 1"
      >
        <a>
          Server Files
          <div class="small">Customize your server files to suit your app</div>
        </a>
      </router-link>

      <router-link
        :to="{ name: 'site.language-settings', params: { site: siteId } }"
        tag="li"
      >
        <a>
          Language Settings
          <div class="small">
            Customize your server to handle bigger uploads etc.
          </div>
        </a>
      </router-link>

      <router-link
        :to="{ name: 'site.server-features', params: { site: siteId } }"
        tag="li"
      >
        <a>
          Server Features
          <div class="small">
            Advanced server setup, highly customized servers
          </div>
        </a>
      </router-link>
    </ul>
    <div class="tab-content">
      <div role="tabpanel" class="tab-pane active"><slot></slot></div>
    </div>
  </div>
</template>
<script>
export default {
  computed: {
    siteId() {
      return this.$route.params.site;
    },
    site() {
      return this.$store.getters["user/sites/show"](this.siteId);
    },
    siteServers() {
      // return this.$store.getters["user_site_servers/getServers"](
      //   this.$route.params.site,
      // );
    },
    canCreateWorkers() {
      // if (this.siteServers && this.siteServers.length > 0) {
      //   return this.siteServersCanCreateWorker;
      // } else if (
      //   this.siteFeatures &&
      //   this.siteFeatures.WorkerService &&
      //   this.siteFeatures.WorkerService.Supervisor &&
      //   this.siteFeatures.WorkerService.Supervisor &&
      //   this.siteFeatures.WorkerService.Supervisor.enabled == 1
      // ) {
      //   return true;
      // }
      // return false;
      return true;
    },
    siteServersCanCreateWorker() {
      // if (this.siteServers && this.siteServers.length > 0) {
      //   let workerServer = this.siteServers.find((server) => {
      //     return (
      //       server.server_features.WorkerService &&
      //       server.server_features.WorkerService.Supervisor.enabled == 1
      //     );
      //   });
      //   if (workerServer) {
      //     return true;
      //   }
      // }
      // return false;
      return true;
    },
    siteFeatures() {
      return {};
      // return this.$store.state.user_site_server_features.features;
    },
  },
  watch: {
    $route: function() {
      // $("#middle .section-content").scrollTop(0);
    },
  },
};
</script>

<template>
    <div class="tab-container tab-left">
        <ul class="nav nav-tabs" v-if="workFlowCompleted === true">

            <router-link :to="{ name : 'site_environment_variables', params : { site_id : siteId } }" tag="li" exact>
                <a>
                    Environment Variables
                    <div class="small">Add environment variables for your site</div>
                </a>
            </router-link>

            <router-link :to="{ name : 'site_cron_jobs', params : { site_id : siteId } }" tag="li" exact>
                <a>
                    Cron Jobs
                    <div class="small">Configure cron jobs</div>
                </a>
            </router-link>

            <router-link :to="{ name : 'site_daemons', params : { site_id : siteId } }" tag="li" exact>
                <a>
                    Daemons
                    <div class="small">Configure daemons processes</div>
                </a>
            </router-link>

            <router-link :to="{ name : 'site_workers', params : { site_id : siteId } }" tag="li" exact>
                <a>
                    Workers
                    <div class="small">Configure workers processes</div>
                </a>
            </router-link>

            <router-link :to="{ name : 'site_server_files', params : { site_id : siteId } }" tag="li" v-if="siteServers.length <= 1">
                <a>
                    Server Files
                    <div class="small">Customize your server files to suit your app</div>
                </a>
            </router-link>

            <router-link :to="{ name : 'site_language_settings', params : { site_id : siteId } }" tag="li">
                <a>
                    Language Settings
                    <div class="small">Customize your server to handle bigger uploads etc.</div>
                </a>
            </router-link>

            <router-link :to="{ name : 'site_server_features', params : { site_id : siteId } }" tag="li">
                <a>
                    Server Features
                    <div class="small">Advanced server setup, highly customized servers</div>
                </a>
            </router-link>

        </ul>
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active">
                <slot></slot>
            </div>
        </div>
    </div>
</template>
<script>
export default {
  computed: {
    siteId() {
      return this.$route.params.site_id;
    },
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

      return [];
    },
  },
  watch: {
    $route: function() {
      $("#middle .column--content").scrollTop(0);
    },
  },
};
</script>

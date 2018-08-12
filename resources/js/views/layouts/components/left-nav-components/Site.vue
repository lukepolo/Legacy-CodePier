<template>
    <router-link :to="{ name: 'site_overview', params : { site_id : site.id} }">
        <div class="site-name">
            <tooltip
                    class="event-status"
                    :class="{
                        'event-status-neutral' : site.last_deployment_status === 'Queued',
                        'event-status-success' : site.last_deployment_status === 'Completed',
                        'event-status-error' : site.last_deployment_status === 'Failed',
                        'icon-spinner' : site.last_deployment_status === 'Running'
                    }"
                    :message="deploymentStatus"
                    placement="right"
            >
            </tooltip>
            {{ site.name }}
            <site-deploy :site="site"></site-deploy>
        </div>
    </router-link>
</template>

<script>
import SiteDeploy from "./SiteDeploy.vue";

export default {
  props: ["site"],
  components: {
    SiteDeploy,
  },
  computed: {
    deploymentStatus() {
      let status = null;

      switch (this.site.last_deployment_status) {
        case "Completed":
          status = "All Good";
          break;
        case "Failed":
          status = "Something Failed";
          break;
        case "Queued":
          status = "Queued";
          break;
        default:
          status = "Deploying";
          break;
      }

      return status;
    },
  },
};
</script>

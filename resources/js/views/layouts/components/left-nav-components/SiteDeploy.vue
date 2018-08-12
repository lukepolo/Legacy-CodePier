<template>
   <span class="deploy-status">
       <template v-if="site.repository && hasDeployableServers && siteActionsEnabled">
           <tooltip message="Deploy Site" placement="left">
               <a href="#" @click.prevent="deploySite" :class="{ 'btn-disabled' : isDeploying }">
                   <span class="icon-deploy"></span>
               </a>
               <template v-if="isDeploying">
                   <span class="deploy-status-text"> {{ isDeploying.status }}</span>
               </template>
           </tooltip>
       </template>
   </span>
</template>

<script>
export default {
  props: ["site"],
  created() {
    this.$store.dispatch("user/sites/servers/get", {
      site: this.site.id,
    });
  },
  methods: {
    deploySite: function() {
      if (!this.isDeploying) {
        this.$store.dispatch("user_site_deployments/deploy", this.site.id);
      }
    },
  },
  computed: {
    hasDeployableServers() {
      return this.site.is_deployable;
    },
    isDeploying() {
      const status = this.site.last_deployment_status;
      return status === "Running" || status === "Queued";
    },
  },
};
</script>

<template>
   <span class="deploy-status">
       <template v-if="site.repository && hasDeployableServers">
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
        props : ['site'],
        methods: {
            deploySite: function () {
                if(!this.isDeploying) {
                    this.$store.dispatch('deploySite', this.site.id)
                }
            },
        },
        computed : {
            hasDeployableServers() {
                let deployableServers = this.$store.state.sitesStore.site_servers[this.site.id]
                if(deployableServers && _.keys(deployableServers).length) {
                    return true
                }
                return false
            },
            isDeploying() {
                return _.find(this.$store.state.sitesStore.running_deployments[this.site.id], function(deployment) {
                    return deployment.status != 'Completed' && deployment.status != 'Failed'
                });

                return false
            }
        },
        created() {
            this.$store.dispatch('getSiteServers', this.site.id);
        }
    }
</script>

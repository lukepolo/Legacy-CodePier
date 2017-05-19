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
                    this.$store.dispatch('user_site_deployments/deploy', this.site.id)
                }
            },
        },
        computed : {
            hasDeployableServers() {
                let deployableServers = this.$store.state.user_site_servers.servers[this.site.id]
                if(deployableServers && _.keys(deployableServers).length) {
                    return true
                }
                return false
            },
            isDeploying() {
                let status = this.site.last_deployment_status
                return status === 'Running' || status === 'Queued'
            }
        },
        created() {
            this.$store.dispatch('user_site_servers/get', this.site.id);
        }
    }
</script>

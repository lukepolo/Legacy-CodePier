<template>
    <div v-if="serverId">
        <div class="tab-container tab-left">
            <ul class="nav nav-tabs">


                <router-link :to="{ name : 'server_info', params : { server_id : serverId } }" tag="li" class="wizard-item" exact>
                    <a>
                        Basic Information
                    </a>
                </router-link>

                <router-link :to="{ name : 'server_sites', params : { server_id : serverId } }" tag="li" class="wizard-item">
                    <a>
                        Sites
                        <div class="small">Connected Sites</div>
                    </a>
                </router-link>

                <!--<router-link :to="{ name : 'server_monitoring', params : { server_id : serverId } }" tag="li" class="wizard-item" v-if="local">-->
                    <!--<a>-->
                        <!--Monitoring-->
                        <!--<div class="small">Server statistics</div>-->
                    <!--</a>-->
                <!--</router-link>-->

                <router-link :to="{ name : 'server_databases', params : { server_id : serverId } }" tag="li">
                    <a>
                        Databases
                        <div class="small">Setup your servers's databases and users</div>
                    </a>
                </router-link>

                <!--<router-link :to="{ name : 'server_buoys', params : { server_id : serverId } }" tag="li" class="wizard-item">-->
                    <!--<a>-->
                        <!--Buoys-->
                        <!--<div class="small">Installed buoys on the system</div>-->
                    <!--</a>-->
                <!--</router-link>-->

                <router-link :to="{ name : 'server_private_ip_addresses', params : { server_id : serverId } }" tag="li" class="wizard-item">
                    <a>
                        Private IP Addresses
                        <div class="small">Add / Remove Private IP Addresses</div>
                    </a>
                </router-link>
            </ul>
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active">
                    <slot></slot>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
  computed: {
    serverId() {
      return this.$route.params.server_id;
    }
  },
  watch: {
    $route: function() {
      $("#middle .section-content").scrollTop(0);
    }
  }
};
</script>

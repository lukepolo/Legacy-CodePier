<template>
    <div class="server">
        <div class="server--header">
            <div>
              <div class="server--name">
                <router-link :to="{ name : 'server_info', params : { server_id : server.id } }">
                    {{ server.name }} <small>({{ serverType }})</small>
                </router-link>
              </div>
              <div class="server--ip">
                  {{ server.ip }} &nbsp;
                  <small v-if="privateIps">
                      ({{ privateIps }})
                  </small>
              </div>
            </div>

            <span class="icon-arrow-down pull-right" :class="{ closed : !showServerInfo }" @click="toggle"></span>
        </div>

        <condensed-server-stats :server="server" v-if="!showServerInfo"></condensed-server-stats>

        <div class="server-info" v-if="showServerInfo">
            <div class="server--status">
                <template v-if="server.progress < 100">

                    <h4>Status</h4>

                    <div class="server-progress-container">
                        <div class="server-progress-number">{{ server.progress}}%</div>
                        <div class="server-progress" :style="{ width : server.progress+'%' }"></div>
                    </div>

                    <div v-if="currentProvisioningStep">
                        <div class="server--status-text text-error" v-if="currentProvisioningStep.failed">
                            Failed {{ currentProvisioningStep.step}}
                        </div>
                        <div v-if="currentProvisioningStep.failed"class="server-btns">
                          <span @click="retryProvision" class="btn btn-small text-center"><span class="icon-refresh"></span> Retry</span>
                        </div>

                        <div class="server--status-text" v-else>
                            {{ server.status }}
                        </div>
                    </div>
                    <div class="server--status-text" v-else>
                        {{ server.status }}
                    </div>

                    <template v-if="server.progress === 0 && server.custom_server_url">
                        <textarea rows="4" readonly>{{ server.custom_server_url }}</textarea>
                        <div class="server-btns">
                          <tooltip message="Login via ssh into your server , and paste this command into your terminal" size="medium" placement="top-left">
                              <span class="fa fa-info-circle"></span>
                          </tooltip>
                          &nbsp;
                          <clipboard :data="server.custom_server_url"></clipboard>
                        </div>
                    </template>
                </template>

                <template v-else>
                    <latest-disk-usage :server="server"></latest-disk-usage>
                    <latest-memory-allocation :server="server"></latest-memory-allocation>
                    <latest-cpu-load :server="server"></latest-cpu-load>
                </template>

            </div>

            <div class="btn-container" v-if="server.progress >= 100">

                <tooltip message="Restart server">
                    <confirm-sidebar dispatch="user_server_services/restart" :params="server.id"><span class="icon-server"></span></confirm-sidebar>
                </tooltip>

                <tooltip message="Restart web services" placement="top-right">
                    <confirm-sidebar dispatch="user_server_services/restartWebServices" :params="server.id" :class="{ disabled : server.type !== 'full_stack' && server.type !== 'web' }">
                        <span class="icon-web"></span>
                    </confirm-sidebar>
                </tooltip>

                <tooltip message="Restart databases">
                    <confirm-sidebar dispatch="user_server_services/restartDatabases" :params="server.id" :class="{ disabled : server.type !== 'full_stack' && server.type !== 'database' }">
                        <span class="icon-database"></span>
                    </confirm-sidebar>
                </tooltip>

                <tooltip message="Restart workers & daemons">
                    <confirm-sidebar dispatch="user_server_services/restartWorkers" :params="server.id" :class="{ disabled : server.type !== 'full_stack' && server.type !== 'worker' }">
                        <span class="icon-worker"></span>
                    </confirm-sidebar>
                </tooltip>

                <tooltip message="Archive server" placement="top-left">
                    <confirm-sidebar dispatch="user_servers/archive" :params="server.id">
                        <span class="icon-archive"></span>
                    </confirm-sidebar>
                </tooltip>
            </div>
        </div>
    </div>
</template>

<script>
import LatestCpuLoad from './server-stats-components/cpu-load/LatestCpuLoad'
import CondensedServerStats from './server-stats-components/CondensedServerStats'
import LatestDiskUsage from './server-stats-components/disk-usage/LatestDiskUsage'
import LatestMemoryAllocation from './server-stats-components/memory-allocation/LatestMemoryAllocation'

export default {
  props: {
    server: {},
    showInfo: {
      default: false
    }
  },
  data() {
    return {
      showing: this.server.progress < 100 ? true : this.showInfo
    };
  },
  components: {
    LatestCpuLoad,
    LatestDiskUsage,
    CondensedServerStats,
    LatestMemoryAllocation,
  },
  created() {
    this.$store.dispatch('user_server_stats/get', this.server.id)

    if (this.server.progress < 100) {
      this.$store.dispatch(
        "user_server_provisioning/getCurrentStep",
        this.server.id
      );
    }
  },
  computed: {
    privateIps() {
      if(this.server.private_ips && this.server.private_ips.length > 0) {
        return this.server.private_ips.join(', ');
      }
    },
    serverType() {
      return _.replace(this.server.type, "_", " ");
    },
    showServerInfo() {
      return this.showing;
    },
    currentProvisioningStep() {
      return this.$store.state.user_server_provisioning.current_step;
    },
  },
  methods: {
    toggle() {
      this.showing = !this.showing;
    },
    retryProvision() {
      this.$store.dispatch("user_server_provisioning/retry", this.server.id);
    },
  }
};
</script>

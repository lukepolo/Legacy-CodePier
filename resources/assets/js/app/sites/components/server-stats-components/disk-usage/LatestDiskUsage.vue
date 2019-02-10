<template>
    <div>
        <h4>Disk Usage</h4>
        <template v-if="diskUsage">
            <div class="server-info condensed" v-for="(stats, disk) in diskUsage">
                {{ disk }}
                <disk-usage :stats="stats[stats.length - 1]"></disk-usage>
            </div>
        </template>
        <template v-else>
            <div class="server-info condensed">
                <div class="server-progress-container">
                    <div class="server-progress"></div>
                    <div class="stats-label stats-used">N/A</div>
                </div>
            </div>
        </template>
    </div>
</template>

<script>
    import DiskUsage from './DiskUsage'
    export default {
      components : {
        DiskUsage
      },
      props : {
        server : {
          required : true,
        }
      },
      computed : {
        stats() {
          let stats = this.$store.state.user_server_stats.stats;
          if(stats.hasOwnProperty(this.server.id)) {
            return stats[this.server.id];
          }
        },
        diskUsage() {
          return this.stats && this.stats.disk_stats;
        },
      }
    }
</script>
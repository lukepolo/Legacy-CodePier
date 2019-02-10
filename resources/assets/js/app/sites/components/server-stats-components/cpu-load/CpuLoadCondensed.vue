<template>
    <tooltip class="server-info condensed" message="Latest CPU Load" delay=.5 v-if="latestLoad">
        <div class="server-progress-container">
            <div
                class="server-progress"
                :class="{
                    danger : getCpuLoad(latestLoad) >= 75,
                    warning :  getCpuLoad(latestLoad) < 75 && getCpuLoad(latestLoad) >= 50
                }"
                :style="{ width : getCpuLoad(latestLoad) + '%' }">
            </div>
        </div>
    </tooltip>
</template>

<script>
  export default {
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
<template>
    <tooltip class="server-info condensed" message="Disk Usage" delay=.5 v-if="highestDiskUsagePercentage">
        <div class="server-progress-container">
            <div
            class="server-progress"
            :class="{
                danger : highestDiskUsagePercentage >= 75,
                warning : highestDiskUsagePercentage < 75 && highestDiskUsagePercentage >= 50
            }"
            :style="{ width : `${highestDiskUsagePercentage}%` }"></div>
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
      highestDiskUsagePercentage() {
        if (this.diskUsage) {
          let highestDiskPercentage = null;
          for(let disk in this.diskUsage) {
            let latestDiskStat = this.diskUsage[disk][this.diskUsage[disk].length - 1]
            if(!highestDiskPercentage || highestDiskPercentage < parseInt(latestDiskStat.percent)) {
              highestDiskPercentage = parseInt(latestDiskStat.percent);
            }
          }
          return highestDiskPercentage;
        }
      },
    }
  }
</script>
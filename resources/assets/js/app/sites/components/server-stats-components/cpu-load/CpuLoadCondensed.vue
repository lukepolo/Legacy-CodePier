<template>
    <tooltip class="server-info condensed" message="Latest CPU Load" delay=.5 v-if="latestLoad">
        <div class="server-progress-container">
            <div
                class="server-progress"
                :class="{
                    danger : cpuLoad >= 75,
                    warning : cpuLoad < 75 && cpuLoad >= 50
                }"
                :style="{ width : `${cpuLoad}%` }">
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
      latestLoad() {
        return this.loadStats && this.loadStats[1]
      },
      stats() {
        let stats = this.$store.state.user_server_stats.stats;
        if(stats.hasOwnProperty(this.server.id)) {
          return stats[this.server.id];
        }
      },
      cpuLoad() {
        return ((this.latestLoad / this.numberOfCpus) * 100).toFixed(2);
      },
      numberOfCpus() {
        return this.stats && this.stats.number_of_cpus;
      },
      loadStats() {
        return this.stats && this.stats.load_stats && this.stats.load_stats[this.stats.load_stats.length - 1];
      },
    }
  }
</script>
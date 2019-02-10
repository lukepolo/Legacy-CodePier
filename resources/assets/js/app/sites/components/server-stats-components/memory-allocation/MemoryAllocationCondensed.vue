<template>
    <tooltip class="server-info condensed" message="Memory Allocation" delay=.5 v-if="highestMemoryAllocationPercentage">
        <div class="server-progress-container">
            <div
                class="server-progress"
                :class="{
                    danger : highestMemoryAllocationPercentage >= 75,
                    warning : highestMemoryAllocationPercentage < 75 && highestMemoryAllocationPercentage >= 50
                }"
                :style="{ width : `${highestMemoryAllocationPercentage}%` }"
            ></div>
        </div>
    </tooltip>
</template>

<script>
  import memoryMixin from './mixins/memoryMixin'
  export default {
    mixins : [memoryMixin],
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
      memoryStats() {
        return this.stats && this.stats.memory_stats;
      },
      highestMemoryAllocationPercentage() {
        if (this.memoryStats) {
          let highestMemoryAllocationPercentage = null;
          for(let memoryName in this.memoryStats) {
            let latestMemoryStat = this.memoryStats[memoryName][this.memoryStats[memoryName].length - 1]
            let percentage = this.memoryUsage(latestMemoryStat);
            if(!highestMemoryAllocationPercentage || highestMemoryAllocationPercentage < percentage) {
              highestMemoryAllocationPercentage = percentage;
            }
          }
          return highestMemoryAllocationPercentage;
        }
      },
    }
  }
</script>
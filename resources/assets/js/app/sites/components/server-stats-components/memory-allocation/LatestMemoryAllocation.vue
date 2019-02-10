<template>
    <div>
        <h4>Memory Allocation</h4>
        <template v-if="memoryStats">
            <div class="server-info condensed" v-for="(stats, memoryName) in memoryStats">
                {{ memoryName }}
                <memory-allocation :stats="stats[stats.length - 1]"></memory-allocation>
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
    import MemoryAllocation from './MemoryAllocation'
    export default {
      components: { MemoryAllocation },
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
      }
    }
</script>
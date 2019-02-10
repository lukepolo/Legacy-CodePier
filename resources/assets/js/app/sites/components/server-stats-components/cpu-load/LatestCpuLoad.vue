<template>
    <div>
        <h4>
            <tooltip message="Number of CPUs on the server" placement="top-right">
                <span class="fa fa-info-circle"></span>
            </tooltip>
            CPU Load
            <em v-if="numberOfCpus">
                ( {{ numberOfCpus }} )
            </em>
        </h4>

        <template v-if="loadStats">
            <cpu-loads
                :stats="loadStats"
                :number-of-cpus="numberOfCpus"
            ></cpu-loads>
        </template>

        <template v-else>
            <div class="server-info condensed">
                <div class="cpu-load">
                    <div class="cpu-group">
                        <div class="cpu-min">1 min</div>
                        <div class="cpu-stats">
                            <div class="server-progress-container">
                                <div class="server-progress"></div>
                                <div class="stats-label stats-available">N/A</div>
                            </div>
                        </div>

                    </div>
                    <div class="cpu-group">
                        <div class="cpu-min">5 mins</div>
                        <div class="cpu-stats">
                            <div class="server-progress-container">
                                <div class="server-progress"></div>
                                <div class="stats-label stats-available">N/A</div>
                            </div>
                        </div>
                    </div>
                    <div class="cpu-group">
                        <div class="cpu-min">15 mins</div>
                        <div class="cpu-stats">
                            <div class="server-progress-container">
                                <div class="server-progress"></div>
                                <div class="stats-label stats-available">N/A</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>
</template>

<script>
    import CpuLoads from './CpuLoads'
    export default {
      components : {
        CpuLoads
      },
      props : {
        server : {
          required : true,
        }
      },
      computed : {
        latestLoad() {
          if (this.stats && this.stats.loads) {
            return this.stats.loads[1];
          }
        },
        stats() {
          let stats = this.$store.state.user_server_stats.stats;
          if(stats.hasOwnProperty(this.server.id)) {
            return stats[this.server.id];
          }
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
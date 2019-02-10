<template>
    <div class="server-info condensed">
        <div class="cpu-load">
            <template v-for="(load, ago) in cpuStats">
                <cpu-load
                    :ago="ago"
                    :load="load"
                    :show-labels="showLabels"
                    :number-of-cpus="numberOfCpus"
                    :updated-at="stats.updated_at"
                ></cpu-load>
            </template>
        </div>
    </div>
</template>

<script>
import CpuLoad from './CpuLoad'
export default {
  components: { CpuLoad },
  props: {
    stats: {
      required : true,
    },
    showLabels: {
      default: true
    },
    numberOfCpus : {
      required : true,
    }
  },
  computed : {
    cpuStats() {
      let stats = Object.assign({}, this.stats);
      if(stats) {
        delete stats.updated_at
        return stats;
      }
    }
  }
};
</script>

<template>
    <div class="server-info condensed">
        <div class="cpu-load">
            <template v-for="(load, ago) in stats.loads">
                <div class="cpu-group">
                    <div class="cpu-min" v-if="showLabels === true">
                        <time-ago :time="getTime(ago)"></time-ago>
                    </div>
                    <div class="cpu-stats">
                        <div class="server-progress-container">
                            <div
                                class="server-progress"
                                :class="{
                                    danger : getCpuLoadPercentage(load) >= 75,
                                    warning :  getCpuLoadPercentage(load) < 75 && getCpuLoadPercentage(load) >= 50
                                }"
                                :style="{ width : getCpuLoadPercentage(load) + '%' }">
                            </div>
                            <div class="stats-label stats-available">{{ getCpuLoad(load) }}%</div>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>
</template>

<script>
export default {
  props: {
    stats: {},
    showLabels: {
      default: true
    },
    lastUpdatedAt: {
      default: null
    }
  },
  methods: {
    getCpuLoad(load) {
      return ((load / this.stats.cpus) * 100).toFixed(2);
    },
    getCpuLoadPercentage(load) {
      let loadPercent = this.getCpuLoad(load);
      return loadPercent > 100 ? 100 : loadPercent;
    },
    getTime(ago) {
      if (this.lastUpdatedAt) {
        return this.parseDate(this.lastUpdatedAt).add(-ago, "minutes");
      }

      return moment().add(-ago, "minutes");
    }
  }
};
</script>

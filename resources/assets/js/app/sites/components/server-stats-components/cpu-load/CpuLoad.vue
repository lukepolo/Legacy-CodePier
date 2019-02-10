<template>
    <div class="cpu-group">
        <div class="cpu-min" v-if="showLabels === true">
            <time-ago :time="timeAgo"></time-ago>
        </div>
        <div class="cpu-stats">
            <div class="server-progress-container">
                <div
                    class="server-progress"
                    :class="{
                        danger : percentage >= 75,
                        warning :  percentage < 75 && percentage >= 50
                    }"
                    :style="{ width : percentage + '%' }">
                </div>
                <div class="stats-label stats-available">{{ cpuLoad }}%</div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
  props: {
    load: {
      required : true,
    },
    ago : {
      required : true,
    },
    numberOfCpus : {
      required : true
    },
    showLabels : {
      required : true,
      type : Boolean
    },
    updatedAt : {
      required : true,
    }
  },
  computed: {
    percentage() {
      return this.cpuLoad > 100 ? 100 : this.cpuLoad;
    },
    cpuLoad() {
      return ((this.load / this.numberOfCpus) * 100).toFixed(2);
    },
    timeAgo() {
        return this.parseDate(this.updatedAt).add(-this.ago, "minutes");
    },
  },
};
</script>

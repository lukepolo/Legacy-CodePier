<template>
    <div class="server-progress-container">
        <div
            class="server-progress"
            :class="{
                danger : memoryUsage(stats) >= 75,
                warning : memoryUsage(stats) < 75 && memoryUsage(stats) >= 50
            }"
            :style="{
                width : `${memoryUsage(stats)}%`
            }"
        ></div>
        <div class="stats-label stats-used">{{ used }}</div>
        <div class="stats-label stats-available">{{ available }}</div>
    </div>
</template>

<script>
    import memoryMixin from './mixins/memoryMixin'
    import { megaBytesToHumanReadable } from './../../../../../mixins/helpers/file-size'
    export default {
      mixins : [memoryMixin],
        props : {
          stats : {
            required : true
          }
        },
        computed : {
          used() {
            return megaBytesToHumanReadable(parseInt(this.stats.total) - parseInt(this.stats.available));
          },
          available() {
            return megaBytesToHumanReadable(this.stats.total);
          }
        }
    }
</script>
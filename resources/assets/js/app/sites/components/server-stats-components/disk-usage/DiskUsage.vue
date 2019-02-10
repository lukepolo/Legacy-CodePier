<template>
    <div class="server-progress-container">
        <div
            class="server-progress"
            :class="{
                danger : percentage >= 75,
                warning : percentage < 75 && percentage >= 50
            }"
            :style="{ width : `${percentage}%` }"
        ></div>
        <div class="stats-label stats-used">
            {{ used }}
        </div>
        <div class="stats-label stats-available">
            {{ available }}
        </div>
    </div>
</template>

<script>
    import { megaBytesToHumanReadable } from './../../../../../mixins/helpers/file-size'
    export default {
        props : {
          stats : {
            required : true
          }
        },
        computed : {
          percentage() {
            return parseInt(this.stats.percent);
          },
          used() {
            return megaBytesToHumanReadable(this.stats.used);
          },
          available() {
            return megaBytesToHumanReadable(parseInt(this.stats.used) + parseInt(this.stats.available));
          }
        }
    }
</script>
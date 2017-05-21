<template>
    <div class="server-info condensed">
        <div class="cpu-load">
            <template v-for="(load, ago) in stats.loads">
                <div class="cpu-group">
                    <div class="cpu-min" v-if="showLabels == true">
                        <time-ago :time="getTime(ago)"></time-ago>
                    </div>
                    <div class="cpu-stats">
                        <div class="server-progress-container">
                            <div
                                class="server-progress"
                                :class="{
                                    danger : getCpuLoad(load) >= 75,
                                    warning :  getCpuLoad(load) < 75 && getCpuLoad(load) >= 50
                                }"
                                :style="{ width : getCpuLoad(load) + '%' }">
                            </div>
                            <div class="stats-label stats-available">{{ load }}</div>
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
            'stats' : {},
            'showLabels' : {
                default : true
            },
            'lastUpdatedAt' : {
                default : null
            },
        },
        methods : {
            getCpuLoad(load) {
                let loadPercent = (load / this.stats.cpus) * 100
                return (loadPercent > 100 ? 100 : loadPercent)
            },
            getTime(ago) {
                if(this.lastUpdatedAt) {
                    return this.parseDate(this.lastUpdatedAt).add(-ago, 'minutes')
                }

                return moment().add(-ago, 'minutes')
            }
        }
    }
</script>
<template>
    <div class="server">
        <div class="server-header">
            <div class="server-name">
                <a class="event-status" :class="{ 'event-status-success' : server.ssh_connection, 'event-status-warning' : !server.ssh_connection && server.ip, 'event-status-neutral' : !server.ssh_connection && !server.ip }" data-toggle="tooltip" data-placement="top" data-container="body" title="" data-original-title="Connection Successful"></a>
                <router-link :to="{ name : 'server_sites', params : { server_id : server.id } }">
                    {{ server.name }}
                </router-link>
            </div>
            <div class="server-ip pull-right">
                <router-link :to="{ name : 'server_sites', params : { server_id : server.id } }">
                    {{ server.ip }}
                </router-link>
            </div>
            <div class="server-provider">
                Digital Ocean
            </div>
        </div>

        <div class="server-info">
            <div class="server-status">
                <template v-if="server.progress < 100">

                    <h4>Status</h4>

                    <div class="server-progress-container">
                        <div class="server-progress-number">{{ server.progress}}%</div>
                        <div class="server-progress" :style="{ width : server.progress+'%' }"></div>
                    </div>

                    <div v-if="currentProvisioningStep">
                        <div class="server-status-text" v-if="currentProvisioningStep.failed">
                            Failed {{ currentProvisioningStep.step}}
                            <div @click="retryProvision" class="btn btn-xs">retry</div>
                        </div>
                        <div class="server-status-text" v-else>
                            {{ server.status }}
                        </div>
                    </div>

                    <template v-if="server.progress == 0 && server.custom_server_url">
                        <textarea rows="4" readonly>{{ server.custom_server_url }}</textarea>
                    </template>

                </template>

                <template v-if="server.progress >= 100">
                    <h4>Disk Usage</h4>
                    <template v-if="server.stats && server.stats.disk_usage">

                        <div class="server-info condensed" v-for="(stats, disk) in server.stats.disk_usage">
                            {{ disk }}
                            <div class="server-progress-container">
                                <div class="server-progress" :style="{ width : stats.percent }"></div>
                                <div class="stats-label stats-used">{{ stats.used }}</div>
                                <div class="stats-label stats-available">{{ stats.available }}</div>
                            </div>
                        </div>

                    </template>
                    <template v-else>
                        <div class="server-info">
                            N/A
                        </div>
                    </template>

                    <h4>Memory</h4>
                    <template v-if="server.stats && server.stats.memory">

                        <div class="server-info condensed" v-for="(stats, memory_name) in server.stats.memory">
                            {{ memory_name }} : {{ stats.used }} / {{ stats.total }}
                            <div class="server-progress-container">
                                <div class="server-progress" :style="{ width : (getBytesFromString(stats.used)/getBytesFromString(stats.total))*100+'%' }"></div>
                                <div class="stats-label stats-used">{{stats.used}}</div>
                                <div class="stats-label stats-available">{{stats.total}}</div>
                            </div>
                        </div>

                    </template>
                    <template v-else>
                        <div class="server-info">
                            N/A
                        </div>
                    </template>

                    <!-- todo - need helps with setup -->
                    <template v-if="server.stats && server.stats.loads">
                        <h4>CPU Load <em>( {{ server.stats.cpus }} )</em></h4>
                        <div class="server-info condensed">
                            <div class="cpu-load">

                                <template v-for="(load, ago) in server.stats.loads">

                                    <div class="cpu-group">

                                        <div class="cpu-min">{{ ago }} {{ getAgoText(ago) }}</div>

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
                    <template v-else>
                        N/A
                    </template>
                </template>

            </div>

            <div class="btn-container">
                <confirm dispatch="restartServerWebServices" :params="server.id"><span class="icon-web"></span></confirm>
                <confirm dispatch="restartServer" :params="server.id"><span class="icon-server"></span></confirm>
                <confirm dispatch="restartServerDatabases" :params="server.id"><span class="icon-database"></span></confirm>
                <confirm dispatch="restartServerWorkers" :params="server.id"><span class="icon-worker"></span></confirm>
                <confirm dispatch="archiveServer" :params="server.id"><span class="icon-archive"></span></confirm>
            </div>
        </div>
</template>

<script>
    export default {
        props : ['server'],
        computed : {
            currentProvisioningStep() {
                let provisioningSteps = this.$store.state.serversStore.servers_current_provisioning_step;

                if(_.has(provisioningSteps, this.server.id)) {
                   return _.get(provisioningSteps, this.server.id);
                }

                return null;
            }
        },
        methods : {
            retryProvision() {
                this.$store.dispatch('retryProvisioning', this.server.id);
            },
            getCpuLoad(load) {
                let loadPercent = (load / this.server.stats.cpus) * 100
                return (loadPercent > 100 ? 100 : loadPercent)
            },
            getAgoText(ago) {
                return _('min').pluralize(ago).replace('"', '')
            }
        },
    }
</script>
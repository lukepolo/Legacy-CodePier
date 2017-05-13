<template>
    <div class="server">
        <div class="server-header">
            <div class="server-name">
                <span class="icon-arrow-down pull-right" :class="{ closed : !showServerInfo }" @click="showInfo = !showInfo"></span>
                <a class="event-status" :class="{ 'event-status-success' : server.ssh_connection, 'event-status-warning' : !server.ssh_connection && server.ip, 'event-status-neutral' : !server.ssh_connection && !server.ip }" data-toggle="tooltip" data-placement="top" data-container="body" title="" data-original-title="Connection Successful"></a>
                <router-link :to="{ name : 'server_sites', params : { server_id : server.id } }">
                    {{ server.name }}
                </router-link>
            </div>
            <div class="server-ip">
                {{ server.ip }} &nbsp;
            </div>

            <template v-if="server.stats && server.stats.loads && !showServerInfo">
                <cpu-loads :stats="server.stats" showLabels="false"></cpu-loads>
            </template>
        </div>

        <div class="server-info" v-if="showServerInfo">
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
                    <div class="server-status-text" v-else>
                        {{ server.status }}
                    </div>

                    <template v-if="server.progress == 0 && server.custom_server_url">
                        <tooltip message="Login via ssh into your server , and paste this command into your terminal" size="medium" placement="top-right">
                            <span class="fa fa-info-circle"></span>
                        </tooltip>
                        <textarea rows="4" readonly>{{ server.custom_server_url }}</textarea>
                        <clipboard :data="server.custom_server_url"></clipboard>
                    </template>

                </template>

                <template v-if="server.progress >= 100">
                    <h4>Disk Usage</h4>
                    <template v-if="server.stats && server.stats.disk_usage">

                        <div class="server-info condensed" v-for="(stats, disk) in server.stats.disk_usage">
                            {{ disk }}
                            <div class="server-progress-container">
                                <div
                                    class="server-progress"
                                    :class="{
                                        danger : parseInt(stats.percent) >= 75,
                                        warning : parseInt(stats.percent) < 75 && parseInt(stats.percent) >= 50
                                    }"
                                    :style="{ width : stats.percent }"></div>
                                <div class="stats-label stats-used">{{ stats.used }}</div>
                                <div class="stats-label stats-available">{{ stats.available }}</div>
                            </div>
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

                    <h4>Memory</h4>
                    <template v-if="server.stats && server.stats.memory">

                        <div class="server-info condensed" v-for="(stats, memory_name) in server.stats.memory">
                            {{ memory_name }}
                            <div class="server-progress-container">
                                <div
                                    class="server-progress"
                                    :class="{
                                        danger : getMemoryUsage(stats) >= 75,
                                        warning : getMemoryUsage(stats) < 75 && getMemoryUsage(stats) >= 50
                                    }"
                                    :style="{
                                        width : getMemoryUsage(stats)+'%'
                                    }"
                                ></div>
                                <div class="stats-label stats-used">{{stats.used}}</div>
                                <div class="stats-label stats-available">{{stats.total}}</div>
                            </div>
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

                    <h4>
                        <tooltip message="Number of CPUs on the server" placement="top-right">
                            <span class="fa fa-info-circle"></span>
                        </tooltip>
                        CPU Load
                        <em v-if="server.stats && server.stats.cpus">
                            ( {{ server.stats.cpus }} )
                        </em>
                    </h4>
                    <template v-if="server.stats && server.stats.loads">
                        <cpu-loads :stats="server.stats"></cpu-loads>
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
                </template>

            </div>

            <div class="btn-container">
                <tooltip message="Restart web services" placement="top-right">
                    <confirm-sidebar dispatch="restartServerWebServices" :params="server.id"><span class="icon-web"></span></confirm-sidebar>
                </tooltip>

                <tooltip message="Restart server">
                    <confirm-sidebar dispatch="restartServer" :params="server.id"><span class="icon-server"></span></confirm-sidebar>
                </tooltip>

                <tooltip message="Restart databases">
                    <confirm-sidebar dispatch="restartServerDatabases" :params="server.id"><span class="icon-database"></span></confirm-sidebar>
                </tooltip>

                <tooltip message="Restart workers">
                    <confirm-sidebar dispatch="restartServerWorkers" :params="server.id"><span class="icon-worker"></span></confirm-sidebar>
                </tooltip>

                <tooltip message="Archive server" placement="top-left">
                    <confirm-sidebar dispatch="archiveServer" :params="server.id"><span class="icon-archive"></span></confirm-sidebar>
                </tooltip>
            </div>
        </div>
    </div>
</template>

<script>
    import {CpuLoads} from './../components'
    export default {
        props : {
            'server' : {},
            'showInfo' : {
                default : false
            }
        },
        components : {
          CpuLoads
        },
        computed : {
            showServerInfo() {
                if(this.server.progress < 100) {
                    return true
                }
                return this.showInfo
            },
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
            getMemoryUsage(stats) {
                return (this.getBytesFromString(stats.used)/this.getBytesFromString(stats.total))*100
            }
        },
    }
</script>
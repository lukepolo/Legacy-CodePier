<template>
    <div class="server">
        <div class="server-name">
            <a class="event-status" :class="{ 'event-status-success' : server.ssh_connection, 'event-status-warning' : !server.ssh_connection && server.ip, 'event-status-neutral' : !server.ssh_connection && !server.ip }" data-toggle="tooltip" data-placement="top" data-container="body" title="" data-original-title="Connection Successful"></a>
            <router-link :to="{ name : 'server_sites', params : { server_id : server.id } }">
                {{ server.name }}
            </router-link>
        </div>
        <div class="server-info">
            <div class="server-provider">
                Digital Ocean
            </div>

            <div class="server-ip">
                <router-link :to="{ name : 'server_sites', params : { server_id : server.id } }">
                    {{ server.ip }}
                </router-link>
            </div>

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
                        <div class="server-info" v-for="(stats, disk) in server.stats.disk_usage">
                            {{ disk }} : {{ stats.used }} / {{ stats.available }} ({{ stats.percent }})
                        </div>
                    </template>
                    <template v-else>
                        <div class="server-info">
                            N/A
                        </div>
                    </template>

                    <h4>Memory</h4>
                    <template v-if="server.stats && server.stats.memory">
                        <div class="server-info" v-for="(stats, memory_name) in server.stats.memory">
                            {{ memory_name }} : {{ stats.used }} / {{ stats.total }}
                        </div>
                    </template>
                    <template v-else>
                        <div class="server-info">
                            N/A
                        </div>
                    </template>

                    <h4>CPU Load</h4>
                    <template v-if="server.stats && server.stats.loads">
                        <div class="server-info">
                            1 / 5 / 10 mins for {{ server.stats.cpus }} CPUS
                        </div>

                        <template v-for="(load, ago, index) in server.stats.loads">
                            <div class="server-info">
                                {{ load }}%
                                <template v-if="index != (Object.keys(server.stats.loads).length - 1)">
                                    /
                                </template>
                            </div>
                        </template>
                    </template>
                    <template v-else>
                        N/A
                    </template>
                </template>

            </div>
        </div>



        <div class="server-name">

            <div>





                <div class="dropdown">
                    <button class="btn btn-default btn-xs dropdown-toggle" type="button" data-toggle="dropdown">
                        <i class="icon-server"></i>
                    </button>
                    <ul class="dropdown-menu">
                        <li>
                            <confirm-dropdown dispatch="restartServerWebServices" :params="server.id"><a href="#"><span class="icon-web"></span> Restart Web Services</a></confirm-dropdown>
                        </li>
                        <li>
                            <confirm-dropdown dispatch="restartServer" :params="server.id"><a href="#"><span class="icon-server"></span> Restart Server</a></confirm-dropdown>
                        </li>
                        <li>
                            <confirm-dropdown dispatch="restartServerDatabases" :params="server.id"><a href="#"><span class="icon-database"></span> Restart Databases</a></confirm-dropdown>
                        </li>
                        <li>
                            <confirm-dropdown dispatch="restartServerWorkers" :params="server.id"><a href="#"><span class="icon-worker"></span> Restart Workers</a></confirm-dropdown>
                        </li>
                        <li role="separator" class="divider"></li>
                        <li>
                            <confirm-dropdown dispatch="archiveServer" :params="server.id"><a href="#"><span class="icon-archive"></span> Archive Server</a></confirm-dropdown>
                        </li>
                    </ul>
                </div>
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
            }
        },
        data() {
            return {
                custom_url : null
            }
        }

    }
</script>
<template>
    <section class="event">
        <div class="event-status" :class="{ 'event-status-success' : server.ssh_connection, 'event-status-warning' : !server.ssh_connection && server.ip, 'event-status-neutral' : !server.ssh_connection && !server.ip }"></div>
            <div class="event-name">
                <router-link :to="{ name : 'server_sites', params : { server_id : server.id } }">
                    {{ server.name }} - {{ server.ip }}
                </router-link>
            <div>
            <template v-if="server.progress < 100">
                {{ server.progress}}% Complete
                <section v-if="currentProvisioningStep">
                    <section v-if="currentProvisioningStep.failed">
                        Failed {{ currentProvisioningStep.step}}
                        <div @click="retryProvision" class="btn btn-xs">retry</div>
                    </section>
                    <section v-else>
                        {{ server.status }}
                    </section>
                </section>

                <template v-if="server.progress == 0 && server.custom_server_url">
                    <textarea rows="4" readonly>{{ server.custom_server_url }}</textarea>
                </template>

            </template>

            <template v-if="server.progress >= 100">
                <h4>Disk Usage</h4>
                <template v-if="server.stats && server.stats.disk_usage">
                    <p v-for="(stats, disk) in server.stats.disk_usage">
                        {{ disk }} : {{ stats.used }} / {{ stats.available }} ({{ stats.percent }})
                    </p>
                </template>
                <template v-else>
                    N/A
                </template>

                <h4>Memory</h4>
                <template v-if="server.stats && server.stats.memory">
                    <p v-for="(stats, memory_name) in server.stats.memory">
                        {{ memory_name }} : {{ stats.used }} / {{ stats.total }}
                    </p>
                </template>
                <template v-else>
                    N/A
                </template>

                <h4>CPU Load</h4>
                <template v-if="server.stats && server.stats.loads">
                    <p>1 / 5 / 10 mins for {{ server.stats.cpus }} CPUS</p>

                    <template v-for="(load, ago, index) in server.stats.loads">
                <span>
                    {{ load }}%
                    <template v-if="index != (Object.keys(server.stats.loads).length - 1)">
                        /
                    </template>
                </span>
                    </template>
                </template>
                <template v-else>
                    N/A
                </template>
            </template>

            <div class="dropdown">
                <button class="btn btn-default btn-xs dropdown-toggle" type="button" data-toggle="dropdown">
                    <i class="icon-server"></i>
                </button>
                <ul class="dropdown-menu">
                    <li>
                        <confirm dispatch="restartServer" :params="server.id"><a href="#">Restart Server</a></confirm>
                    </li>
                    <li>
                        <confirm dispatch="restartServerWebServices" :params="server.id"><a href="#">Restart Web Services</a></confirm>
                    </li>
                    <li>
                        <confirm dispatch="restartServerDatabases" :params="server.id"><a href="#">Restart Databases</a></confirm>
                    </li>
                    <li>
                        <confirm dispatch="restartServerWorkers" :params="server.id"><a href="#">Restart Workers</a></confirm>
                    </li>
                    <li role="separator" class="divider"></li>
                    <li>
                        <confirm dispatch="archiveServer" :params="server.id"><a href="#">Archive Server</a></confirm>
                    </li>
                </ul>
            </div>
        </div>
    </section>
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
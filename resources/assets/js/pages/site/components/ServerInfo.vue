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
            </template>

            <h4>Disk Usage</h4>
            <template v-if="server.stats.disk_usage">
                <p v-for="(stats, disk) in server.stats.disk_usage">
                    {{ disk }} : {{ stats.used }} / {{ stats.available }} ({{ stats.percent }})
                </p>
            </template>
            <template v-else>
                N/A
            </template>

            <h4>Memory</h4>
            <template v-if="server.stats.memory">
                <p v-for="(stats, memory_name) in server.stats.memory">
                    {{ memory_name }} : {{ stats.used }} / {{ stats.available }}
                </p>
            </template>
            <template v-else>
                N/A
            </template>

            <h4>CPU Load</h4>
            <template v-if="server.stats.loads">
                <p>1 / 5 / 10 minutes ago</p>
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


            <div class="dropdown">
                <button class="btn btn-default btn-xs dropdown-toggle" type="button" data-toggle="dropdown">
                    <i class="icon-server"></i>
                </button>
                <ul class="dropdown-menu">
                    <li><a href="#">Restart Web Services</a></li>
                    <li><a href="#">Restart Server</a></li>
                    <li><a href="#">Restart Database</a></li>
                    <li><a href="#">Restart Workers</a></li>
                    <li role="separator" class="divider"></li>
                    <li>
                        <confirm>
                            <a href="#">Archive Server</a>
                        </confirm>
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
                var provisioningSteps = this.$store.state.serversStore.servers_current_provisioning_step;

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
        }
    }
</script>
<template>
    <section class="event">
        <div class="event-status" :class="{ 'event-status-success' : server.ssh_connection, 'event-status-warning' : !server.ssh_connection && server.ip, 'event-status-neutral' : !server.ssh_connection && !server.ip }"></div>
            <div class="event-name">
                <router-link :to="{ name : 'server_sites', params : { server_id : server.id } }">
                    {{ server.name }} - {{ server.ip }}
                </router-link>
            <div>
            {{ server.progress}}% Complete
            <section v-if="server.progress < 100 && currentProvisioningStep">
                <section v-if="currentProvisioningStep.failed">
                    Failed {{ currentProvisioningStep.step}}
                    <div @click="retryProvision" class="btn btn-xs">retry</div>
                </section>
                <section v-else>
                    {{ server.status }}
                </section>
            </section>

            <p>4 / 40 GB</p>
            <p>1.2 Avg Load</p>
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
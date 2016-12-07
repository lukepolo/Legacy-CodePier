<template>
    <section class="event">
        <div class="event-status event-status-success"></div>
        <div class="event-name">{{ server.name }} - {{ server.ip }}</div>
        <div>
            ({{ server.progress}}) -
            <section v-if="server.progress < 100 && currentProvisioningStep">
                <section v-if="currentProvisioningStep.failed">
                    Failed {{ currentProvisioningStep.step}}
                    <div @click="retryProvision" class="btn btn-xs">retry</div>
                </section>
                <section v-else>
                    {{ server.status }}
                </section>
            </section>
        </div>
        <div class="event-pile">
            <span class="icon-layers">
                {{ server.pile.name }}
            </span>
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
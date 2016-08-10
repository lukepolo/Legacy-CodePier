<template>
    <div class="server selected">
        <div class="server-name">
            <a :class="{ 'server-success' : server.ssh_connection , 'server-error' : !server.ssh_connection}" class="server-connection" data-toggle="tooltip" data-placement="top" data-container="body" title="Connection Successful"></a>
            {{ server.name }}
        </div>
        <div class="server-info">
            <div class="server-provider">
                {{ server.server_provider.name }}
            </div>

            <div class="server-ip">
                {{ server.ip }}
            </div>

            <div class="server-status">
                <h4>Status</h4>
                <div class="server-progress-container">
                    <div class="server-progress-number">{{ server.progress }}%</div>
                    <div class="server-progress" :style="{ width: server.progress+'%' }"></div>
                </div>
                <div class="server-status-text">
                    {{ server.status }}
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props : ['server'],
        data() {
            return {

            }
        }
    }
</script>
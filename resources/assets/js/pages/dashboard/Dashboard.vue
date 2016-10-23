<template>
    <section>
        <left-nav></left-nav>
        <section id="middle" class="section-column">
            <h3 class="section-header primary">Servers</h3>
            <div class="section-content">
                <table class="table">
                    <thead>
                    <tr>
                        <th>Server</th>
                        <th>Provider</th>
                        <th>Region</th>
                        <th>Status</th>
                        <th>Connection</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="server in servers">
                        <td>
                            <router-link :to="{ name : 'server_sites', params : { server_id : server.id } }">
                                {{ server.name }}
                            </router-link>
                        </td>
                        <td>{{ server.ip }}</td>
                        <td>{{ server.server_provider.name }}</td>
                        <td>REGION TODO</td>
                        <td>{{ server.status }}</td>
                        <td>
                            <div class="server-name">
                                <span :class="{ 'server-success' : server.ssh_connection , 'server-error' : !server.ssh_connection}"
                                      class="server-connection" data-toggle="tooltip" data-placement="top"
                                      data-container="body" title="Connection Successful"></span>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </section>
    </section>
</template>

<script>
    import LeftNav from './../../core/LeftNav.vue';

    export default {
        components: {
            LeftNav
        },
        computed: {
            servers() {
                return this.$store.state.serversStore.servers;
            }
        },
        created() {
            this.$store.dispatch('getServers');
        }
    }
</script>
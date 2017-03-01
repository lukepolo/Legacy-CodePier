<template>
    <section>
        <section id="middle" class="section-column">
            <h3 class="section-header primary">My Servers</h3>
            <div class="section-content">
                <div class="jcf-form-wrap">
                    <div class="container">

                        <template v-if="!showArchived">
                            <router-link :to="{ name: 'archived_servers' }">Archived Servers</router-link>
                        </template>
                        <template v-else>
                            <router-link :to="{ name: 'servers' }">Servers</router-link>
                        </template>

                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>IP</th>
                                    <td>Status</td>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="server in servers">
                                    <td>
                                        <template v-if="server.status == 'Provisioned'">
                                            <router-link :to="{ name : 'server_sites', params : { server_id : server.id } }">
                                                {{ server.name }}
                                            </router-link>
                                        </template>
                                        <template v-else>
                                            {{ server.name }}
                                        </template>
                                    </td>
                                    <td>{{ server.ip }}</td>
                                    <td>
                                        <template v-if="server.deleted_at">
                                            <confirm dispatch="restoreServer" :params="server.id">
                                                Restore
                                            </confirm>
                                        </template>
                                        <template v-else>
                                            <template v-if="server.status != 'Provisioned' && server.custom_server_url">
                                                <textarea rows="4" readonly>{{ server.custom_server_url }}</textarea>
                                                <clipboard :data="server.custom_server_url"></clipboard>
                                            </template>
                                            <template v-else>
                                                {{ server.status }}
                                            </template>
                                            <confirm dispatch="archiveServer" :params="server.id">
                                                Archive Server
                                            </confirm>
                                        </template>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="btn-footer">
                        <router-link :to="{ name : 'server_form' }">
                            <a class="btn btn-primary">Create A Server</a>
                        </router-link>
                    </div>
                </div>
            </div>
        </section>
    </section>
</template>

<script>

    export default {
        created() {
            this.fetchData();
        },
        watch: {
            '$route': 'fetchData'
        },
        methods: {
            fetchData() {
                if(!this.showArchived) {
                    this.$store.dispatch('getServers');
                } else {
                    this.$store.dispatch('getTrashedServers')
                }
            }
        },
        computed: {
            showArchived() {
                return this.$route.name != 'servers'
            },
            servers() {
                if(!this.showArchived) {
                    return this.$store.state.serversStore.servers;
                }

                return this.$store.state.serversStore.trashed_servers;
            }
        }
    }
</script>

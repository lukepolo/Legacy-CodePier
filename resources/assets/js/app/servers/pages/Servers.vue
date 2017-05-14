<template>
    <section>
        <section id="middle" class="section-column">
            <h3 class="section-header primary">My Servers</h3>
            <div class="section-content">
                <div class="jcf-form-wrap">
                    <div class="container">
                        <div class="btn btn-primary" @click="showArchive= !showArchive">
                            Show
                            <template v-if="!showArchive">
                                Archived
                            </template>
                            <template v-else>
                                Unarchived
                            </template>
                        </div>
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
                                            <confirm dispatch="user_servers/restore" :params="server.id">
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
                                            <confirm dispatch="user_servers/archive" :params="server.id">
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
        data() {
            return {
                showArchive : false
            }
        },
        methods: {
            fetchData() {
                this.$store.dispatch('user_servers/getTrashed');
            }
        },
        computed: {
            showArchived() {
                return this.$route.name != 'servers'
            },
            servers() {
                if(!this.showArchive) {
                    return this.$store.state.user_servers.servers;
                }
                return this.$store.state.user_servers.trashed;
            }
        }
    }
</script>

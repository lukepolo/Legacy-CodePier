<template>
    <section>
        <form @submit.prevent="installDaemon()">

            <div class="flyform--group">
                <input type="text" name="command" v-model="form.command" placeholder=" ">
                <label for="command">Command</label>
            </div>

            <div class="flyform--group">
                <label>Select User</label>
                <div class="flyform--group-select">
                    <select name="user" v-model="form.user">
                        <option value="root">Root User</option>
                        <option value="codepier">CodePier User</option>
                    </select>
                </div>
            </div>

            <template v-if="site && displayServerSelection">
                <br>
                <br>
                <br>
                <h3>By default we install these all on all servers, you show pick the servers that you want these to run on</h3>
                <template v-for="server in servers">
                    <div class="flyform--group-checkbox">
                        <label>
                            <input type="checkbox" v-model="form.servers" :value="server.id">
                            <span class="icon"></span>
                            {{ server.name }} ({{ server.ip }})
                        </label>
                    </div>
                </template>
            </template>

            <div class="flyform--footer">
                <div class="flyform--footer-btns">
                    <button class="btn btn-primary" type="submit">Create Daemon</button>
                </div>
            </div>

        </form>


        <br>

        <div v-if="daemons.length">
            <h3>Daemons</h3>

            <table class="table">
                <thead>
                <tr>
                    <th>Command</th>
                    <th>User</th>
                    <th></th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="daemon in daemons">
                    <td class="break-word">{{ daemon.command }}</td>
                    <td>{{ daemon.user }}</td>
                    <td>
                        <template v-if="isRunningCommandFor(daemon.id)">
                            {{ isRunningCommandFor(daemon.id).status }}
                        </template>
                    </td>

                    <td class="table--action">
                        <tooltip message="Delete">
                            <span class="table--action-delete">
                                <a @click="deleteDaemon(daemon.id)"><span class="icon-trash"></span></a>
                            </span>
                        </tooltip>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>


        <input type="hidden" v-if="site">

    </section>
</template>

<script>
    export default {
        data() {
            return {
                form: this.createForm({
                    user: null,
                    command: null,
                    servers : [],
                    server_types : [],
                })
            }
        },
        created() {
            this.fetchData();
        },
        watch: {
            '$route': 'fetchData'
        },
        methods: {
            fetchData() {
                if(this.siteId) {
                    this.$store.dispatch('user_site_daemons/get', this.siteId)
                }

                if(this.serverId) {
                    this.$store.dispatch('user_server_daemons/get', this.serverId)
                }

            },
            installDaemon() {
                if(this.siteId) {
                    this.form.site = this.siteId
                    this.$store.dispatch('user_site_daemons/store', this.form).then((daemon) => {
                        if(daemon.id) {
                            this.resetForm()
                        }
                    })
                }

                if(this.serverId) {
                    this.form.server = this.serverId
                    this.$store.dispatch('user_server_daemons/store', this.form).then((daemon) => {
                        if(daemon.id) {
                            this.resetForm()
                        }
                    })
                }

            },
            deleteDaemon: function (daemon_id) {

                if(this.siteId) {
                    this.$store.dispatch('user_site_daemons/destroy', {
                        daemon: daemon_id,
                        site: this.siteId,
                    });
                }

                if(this.serverId) {
                    this.$store.dispatch('user_server_daemons/destroy', {
                        daemon: daemon_id,
                        server: this.serverId
                    });
                }
            },
            isRunningCommandFor(id) {
                return this.isCommandRunning('App\\Models\\Daemon', id)
            },
            resetForm() {
                this.form.reset()
                if(this.site) {
                    this.form.command = this.site.path
                }
            }
        },
        computed: {
            site() {
                let site = this.$store.state.user_sites.site

                if(site) {
                    this.form.command = site.path + (site.zerotime_deployment ? '/current/' : '/')
                }

                return site
            },
            siteId() {
                return this.$route.params.site_id
            },
            serverId() {
                return this.$route.params.server_id
            },
            daemons() {
                if(this.siteId) {
                    return this.$store.state.user_site_daemons.daemons
                }

                if(this.serverId) {
                    return this.$store.state.user_server_daemons.daemons
                }

            },
            servers() {
                return this.$store.getters['user_site_servers/getServers'](this.$route.params.site_id)
            },
            displayServerSelection() {
                if(this.$route.params.site_id) {
                    if(this.servers && this.servers.length > 1) {
                        return true
                    }
                    return false
                }
            },
        }
    }
</script>
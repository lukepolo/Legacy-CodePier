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

            <server-selection :server_ids.sync="form.server_ids" :server_types.sync="form.server_types"></server-selection>

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
                    <daemon :daemon="daemon" v-for="daemon in daemons" :key="daemon.id"></daemon>
                </tbody>
            </table>
        </div>


        <input type="hidden" v-if="site">

    </section>
</template>

<script>
    import { ServerSelection, Daemon } from "./../components"
    export default {
        components: {
            Daemon,
            ServerSelection
        },
        data() {
            return {
                form: this.createForm({
                    user: null,
                    command: null,
                    server_ids : [],
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
            isRunningCommandFor(id) {
                return this.isCommandRunning('App\\Models\\Daemon', id)
            },
            resetForm() {
                this.form.reset()
                if(this.site) {
                    this.form.command = this.site.path
                }
            },
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
            }
        }
    }
</script>
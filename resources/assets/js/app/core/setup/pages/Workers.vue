<template>
    <section>
        <div class="jcf-form-wrap">
            <form @submit.prevent="installWorker()" class="floating-labels">

                <div class="jcf-input-group">
                    <input type="text" name="command" v-model="form.command">
                    <label for="command">
                        <span class="float-label">Command</span>
                    </label>
                </div>

                <div class="jcf-input-group">
                    <input type="integer" name="number_of_workers" v-model="form.number_of_workers">
                    <label for="number_of_workers">
                        <span class="float-label">Number of Workers</span>
                    </label>
                </div>

                <div class="jcf-input-group">
                    <div class="input-question">
                        Select User
                    </div>
                    <div class="select-wrap">
                        <select name="user" v-model="form.user">
                            <option value="root">Root User</option>
                            <option value="codepier">CodePier User</option>
                        </select>
                    </div>
                </div>

                <div class="jcf-input-group input-checkbox">
                    <div class="input-question">Worker Options</div>
                    <label>
                        <input type="checkbox" name="auto_start" v-model="form.auto_start">
                        <span class="icon"></span>
                        Auto Start
                    </label>
                </div>
                <div class="jcf-input-group input-checkbox">
                    <label>
                        <input type="checkbox" name="auto_restart" v-model="form.auto_restart">
                        <span class="icon"></span>
                        Auto Restart
                    </label>
                </div>

                <div class="btn-footer">
                    <button class="btn btn-primary" type="submit">Create Queue Worker</button>
                </div>

            </form>
        </div>

        <br><br>
        <table class="table">
            <thead>
            <tr>
                <th>Command</th>
                <th>User</th>
                <th>Auto Start</th>
                <th>Auto Restart</th>
                <th>Number of Workers</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="worker in workers">
                <td>{{ worker.command }}</td>
                <td>{{ worker.user }}</td>
                <td>{{ worker.auto_start }}</td>
                <td>{{ worker.auto_restart }}</td>
                <td>{{ worker.number_of_workers }}</td>
                <td>
                    <template v-if="isRunningCommandFor(worker.id)">
                        {{ isRunningCommandFor(worker.id).status }}
                    </template>
                    <template v-else>
                        <a @click="deleteWorker(worker.id)" href="#" class="fa fa-remove">X</a>
                    </template>
                </td>
            </tr>
            </tbody>
        </table>

        <input type="hidden" v-if="site">

    </section>
</template>

<script>
    export default {
        data() {
            return {
                form: {
                    command: null,
                    auto_start: true,
                    auto_restart: true,
                    number_of_workers: 1,
                }
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
                    this.$store.dispatch('user_site_workers/get', this.siteId)
                }

                if(this.serverId) {
                    this.$store.dispatch('user_server_workers/get', this.serverId)
                }

            },
            installWorker() {
                if(this.siteId) {
                    this.form.site = this.siteId
                    this.$store.dispatch('user_site_workers/store', this.form).then((worker) => {
                        if(worker.id) {
                            this.resetForm()
                        }
                    })
                }

                if(this.serverId) {
                    this.form.server = this.serverId
                    this.$store.dispatch('user_server_workers/store', this.form).then((worker) => {
                        if(worker.id) {
                            this.resetForm()
                        }
                    })
                }

            },
            deleteWorker: function (worker_id) {
                if(this.siteId) {
                    this.$store.dispatch('user_site_workers/destroy', {
                        worker: worker_id,
                        site: this.siteId,
                    });
                }

                if(this.serverId) {
                    this.$store.dispatch('user_server_workers/destroy', {
                        worker: worker_id,
                        server: this.serverId
                    });
                }
            },
            isRunningCommandFor(id) {
                return this.isCommandRunning('App\\Models\\Worker', id)
            },
            resetForm() {
                this.form = this.$options.data().form
                if(this.site) {
                    this.form.command = this.site.path
                }
            }
        },
        computed: {
            site() {
                let site = this.$store.state.user_sites.site

                if(site) {
                    this.form.command = site.path
                }

                return site
            },
            siteId() {
                return this.$route.params.site_id
            },
            serverId() {
                return this.$route.params.server_id
            },
            workers() {
                if(this.siteId) {
                    return this.$store.state.user_site_workers.workers
                }

                if(this.serverId) {
                    return this.$store.state.user_server_workers.workers
                }

            }
        }
    }
</script>
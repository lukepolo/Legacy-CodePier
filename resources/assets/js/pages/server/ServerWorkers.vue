<template>
    <section>
        <section id="middle" class="section-column" v-if="server">

            <div class="jcf-form-wrap">
                <form @submit.prevent="createServerWorker" class="floating-labels">
                    <div class="jcf-input-group">
                        <input type="text" name="command" v-model="form.command">
                        <label for="command">
                            <span class="float-label">Command</span>
                        </label>
                    </div>

                    <div class="jcf-input-group">
                        <div class="input-question">Run as User</div>
                        <div class="select-wrap">
                            <select name="user" v-model="form.user">
                                <option value="root">Root User</option>
                                <option value="codepier">CodePier User</option>
                            </select>
                        </div>
                    </div>

                    <div class="jcf-input-group input-checkbox">
                        <div class="input-question">Repository Options</div>
                        <label>
                            <input type="checkbox" name="auto_start" v-model="form.auto_start">
                            <span class="icon"></span>
                            Auto Start
                        </label>
                        <label>
                            <input type="checkbox" name="auto_restart" v-model="form.auto_restart"> Auto Restart
                            <span class="icon"></span>
                            Auto Restart
                        </label>
                    </div>

                    <div class="jcf-input-group">
                        <input type="integer" name="number_of_workers" v-model="form.number_of_workers">
                        <label for="number_of_workers">
                            <span class="float-label">Workers</span>
                        </label>
                    </div>

                    <div class="btn-footer">
                        <button class="btn btn-primary" type="submit">Create Worker</button>
                    </div>
                </form>
            </div>

            <table class="table" v-if="workers" v-for="worker in workers">
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
                <tr>
                    <td>{{ worker.command }}</td>
                    <td>{{ worker.user }}</td>
                    <td>{{ worker.auto_start }}</td>
                    <td>{{ worker.auto_restart }}</td>
                    <td>{{ worker.number_of_workers }}</td>
                    <td><a href="#" class="fa fa-remove" @click="deleteServerWorker(worker.id)">remove</a></td>
                </tr>
                </tbody>
            </table>
        </section>
    </section>
</template>

<script>
    export default {
        data() {
            return {
                form: {
                    user: 'root',
                    command: null,
                    auto_start: true,
                    auto_restart: true,
                    number_of_workers: 1
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
                this.$store.dispatch('getServer', this.$route.params.server_id);
                this.$store.dispatch('getServerWorkers', this.$route.params.server_id);

            },
            createServerWorker() {
                this.form['server'] = this.server.id;
                this.$store.dispatch('createServerWorker', this.form);
            },
            deleteServerWorker(worker_id) {
                this.$store.dispatch('deleteServerWorker', {
                    worker: worker_id,
                    server: this.server.id
                });
            }
        },
        computed: {
            server() {
                return this.$store.state.serversStore.server;
            },
            workers() {
                return this.$store.state.serverWorkersStore.server_workers;
            }
        }
    }
</script>

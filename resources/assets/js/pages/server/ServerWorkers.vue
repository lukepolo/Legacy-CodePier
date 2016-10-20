<template>
    <section>
        <left-nav></left-nav>
        <section id="middle" class="section-column" v-if="server">
            <server-nav :server="server"></server-nav>
            <form @submit.prevent="createServerWorker">
                Command
                <input type="text" name="command" v-model="form.command">
                User
                <select name="user" v-model="form.user">
                    <option value="root">Root User</option>
                    <option value="codepier">CodePier User</option>
                </select>
                <input type="checkbox" name="auto_start" v-model="form.auto_start"> Auto Start
                <input type="checkbox" name="auto_restart" v-model="form.auto_restart"> Auto Restart
                Workers
                <input type="integer" name="number_of_workers" v-model="form.number_of_workers">

                <button type="submit">Create Cron</button>
            </form>

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
    import ServerNav from './components/ServerNav.vue';
    import LeftNav from './../../core/LeftNav.vue';

    export default {
        components: {
            LeftNav,
            ServerNav
        },
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
                return this.$store.state.serversStoreserver;
            },
            workers() {
                return serverWorkerStore.state.server_workers;
            }
        }
    }
</script>

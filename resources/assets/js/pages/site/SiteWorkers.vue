<template>
    <div v-if="site">
        <div class="jcf-form-wrap">
            <form @submit.prevent="installWorker()">
                <h3>Laravel Queue Workers</h3>
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
            </form>
        </div>

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
                <td><a @click="deleteWorker(worker.id)" href="#" class="fa fa-remove">X</a></td>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                form: {
                    site_id: null,
                    command: null,
                    auto_start: null,
                    auto_restart: null,
                    number_of_workers: null,
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
                this.$store.dispatch('getSite', this.$route.params.site_id);
                this.$store.dispatch('getWorkers', this.$route.params.site_id);
            },
            installWorker() {
                this.$store.dispatch('installWorker', this.form);
            },
            deleteWorker: function (worker_id) {
                this.$store.dispatch('deleteWorker', {
                    worker: worker_id,
                    site: this.site.id,
                });
            }
        },
        computed: {
            site() {
                var site = this.$store.state.sitesStore.site;
                if (site) {
                    this.form.site_id = site.id;
                    this.form.command = site.path;
                    this.form.selected_servers = _.map(site.servers, 'id');
                }

                return this.$store.state.sitesStore.site;
            },
            workers() {
                return this.$store.state.sitesStore.workers;
            }
        }
    }
</script>
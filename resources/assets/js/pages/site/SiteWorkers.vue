<template>
    <section>
        <left-nav></left-nav>
        <section id="middle" class="section-column">
            <site-header></site-header>
            <div class="section-content" v-if="site">
                <div class="container">
                    <site-nav></site-nav>

                    Laravel Queue Workers
                    <form @submit.prevent="installWorker()">
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
                    </form>

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
            </div>
        </section>
        <servers></servers>
    </section>
</template>

<script>
    import LeftNav from './../../core/LeftNav.vue';
    import Servers from './components/Servers.vue';
    import SiteNav from './components/SiteNav.vue';
    import SiteHeader from './components/SiteHeader.vue';
    import ServerSelector from './../../components/ServerSelector.vue';
    export default {
        components: {
            LeftNav,
            Servers,
            SiteNav,
            SiteHeader,
            ServerSelector
        },
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
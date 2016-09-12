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
                        Connection :
                        <input name="connection" v-model="form.connection" type="radio" value="sqs"> Amazon SQS
                        <input name="connection" v-model="form.connection" type="radio" value="beanstalkd"> Beanstalk
                        <input name="connection" v-model="form.connection" type="radio" value="database"> Database
                        <input name="connection" v-model="form.connection" type="radio" value="redis"> Redis

                        Queue Channel
                        <input type="text" v-model="form.queue_channel" placeholder="default" name="queue_channel">

                        Maximum Seconds Per Job
                        <input type="text" v-model="form.timeout" name="timeout" placeholder="60">

                        Time interval between jobs (when empty)
                        <input type="text" v-model="form.sleep" placeholder="10" name="sleep">

                        Maximum Tries
                        <input type="text" v-model="form.tries" placeholder="3" name="tries">

                        Run as Daemon
                        <input type="checkbox" v-model="form.daemon" name="daemon">

                        Number Of Workers
                        <input type="number" v-model="form.number_of_workers" name="number_of_workers">

                        <server-selector :servers="site.servers" param="form.selected_servers" feature="WorkerService.Supervisor.enabled" feature_message="This server does not have a worker system installed."></server-selector>

                        <button type="submit">Install Worker</button>
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
                form : {
                    sleep: null,
                    tries: null,
                    daemon: true,
                    timeout: null,
                    site_id : null,
                    auto_start: null,
                    connection: null,
                    auto_restart: null,
                    queue_channel: null,
                    selected_servers : [],
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
        methods : {
            fetchData : function() {
                siteStore.dispatch('getSite', this.$route.params.site_id);
                siteStore.dispatch('getWorkers', this.$route.params.site_id);
            },
            installWorker: function () {
                siteStore.dispatch('installWorker', this.form);
            },
            deleteWorker : function(worker_id) {
                siteStore.dispatch('deleteWorker', {
                    worker : worker_id,
                    site : this.site.id,
                });
            }
        },
        computed: {
            site: function() {
                var site = siteStore.state.site;
                if(site) {
                    this.form.site_id = site.id;
                    this.form.selected_servers = _.map(site.servers, 'id');
                }

                return siteStore.state.site;
            },
            workers: () => {
                return siteStore.state.workers;
            }
        }
    }
</script>
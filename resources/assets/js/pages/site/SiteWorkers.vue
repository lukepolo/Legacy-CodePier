<template>
    <section>
        <left-nav></left-nav>
        <section id="middle" class="section-column">
            <h3 class="section-header primary">Site Repository</h3>
            <div class="section-content" v-if="site">
                <div class="container">
                    <site-nav></site-nav>

                    Laravel Queue Workers
                    <form @submit.prevent="installWorker()">
                        Connection :
                        <input name="connection" v-model="connection" type="radio" value="sqs"> Amazon SQS
                        <input name="connection" v-model="connection" type="radio" value="beanstalkd"> Beanstalk
                        <input name="connection" v-model="connection" type="radio" value="database"> Database
                        <input name="connection" v-model="connection" type="radio" value="redis"> Redis

                        Queue Channel
                        <input type="text" v-model="queue_channel" placeholder="default" name="queue_channel">

                        Maximum Seconds Per Job
                        <input type="text" v-model="timeout" name="timeout" placeholder="60">

                        Time interval between jobs (when empty)
                        <input type="text" v-model="sleep" placeholder="10" name="sleep">

                        Maximum Tries
                        <input type="text" v-model="tries" placeholder="3" name="tries">

                        Run as Daemon
                        <input type="checkbox" v-model="daemon" name="daemon" value="1">

                        Number Of Workers
                        <input type="number" v-model="number_of_workers" name="number_of_workers">

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

    </section>
</template>

<script>
    import LeftNav from './../../core/LeftNav.vue';
    import SiteNav from './components/SiteNav.vue';
    export default {
        components: {
            SiteNav,
            LeftNav,
        },
        data() {
            return {
                sleep: null,
                tries: null,
                daemon: null,
                timeout: null,
                auto_start: null,
                connection: null,
                auto_restart: null,
                queue_channel: null,
                number_of_workers: null
            }
        },
        methods: {
            installWorker: function () {
                siteStore.dispatch('installWorker', {
                    sleep: this.sleep,
                    tries: this.tries,
                    daemon: this.daemon,
                    timeout: this.timeout,
                    site_id: this.site.id,
                    auto_start: this.auto_start,
                    connection: this.connection,
                    auto_restart: this.auto_restart,
                    queue_channel : this.queue_channel,
                    number_of_workers: this.number_of_workers
                });
            },
            deleteWorker : function(worker_id) {
                siteStore.dispatch('deleteWorker', worker_id);
            }
        },
        computed: {
            site: () => {
                return siteStore.state.site;
            },
            workers: () => {
                return siteStore.state.workers;
            }
        },
        mounted() {
            siteStore.dispatch('getSite', this.$route.params.site_id);
            siteStore.dispatch('getWorkers', this.$route.params.site_id);
        }
    }
</script>
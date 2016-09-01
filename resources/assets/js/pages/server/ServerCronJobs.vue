<template>
    <section>
        <left-nav></left-nav>
        <section id="middle" class="section-column" v-if="server">
            <server-nav :server="server"></server-nav>
            <span id="cron-preview"></span>
            <form @submit.prevent="createServerCronJob">
                <input type="text" name="cron" v-model="form.cron">
                <input type="hidden" name="cron_timing" v-model="form.cron_timing">
                <select name="user" v-model="form.user">
                    <option value="root">Root User</option>
                    <option value="codepier">CodePier User</option>
                </select>
                <div v-cronjob></div>
                <button type="submit">Create Cron</button>
            </form>

            <table class="table" v-if="cron_jobs" v-for="cron_job in cron_jobs">
                <thead>
                <tr>
                    <th>Job</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>{{ cron_job.job }}</td>
                    <td><a @click="deleteCronJob(cron_job.id)" href="#" class="fa fa-remove">X</a></td>
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
        components : {
            LeftNav,
            ServerNav
        },
        data() {
            return {
                form : {
                    cron : null,
                    user : 'root',
                    cron_timing: null
                }
            }
        },
        directives: {
            cronjob: {
                bind: function (el) {
                    $(el).cron({
                        onChange: function () {
                            var cronTiming = $(this).cron("value");
                            $('#cron-preview').text(cronTiming);
                            $('input[name="cron_timing"]').val(cronTiming);
                        }
                    });
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
            fetchData: function () {
                serverStore.dispatch('getServer', this.$route.params.server_id);
                serverCronJobStore.dispatch('getServerCronJobs', this.$route.params.server_id);
            },
            createServerCronJob() {
                this.form['server'] = this.server.id;
                serverCronJobStore.dispatch('createServerCronJob', this.form);
            },
            deleteCronJob(cron_job_id) {
                serverCronJobStore.dispatch('deleteServerCronJob', {
                    server: this.server.id,
                    cron_job : cron_job_id
                });
            }
        },
        computed : {
            server : () => {
                return serverStore.state.server;
            },
            cron_jobs : () => {
                return serverCronJobStore.state.server_cron_jobs;
            }
        }
    }
</script>

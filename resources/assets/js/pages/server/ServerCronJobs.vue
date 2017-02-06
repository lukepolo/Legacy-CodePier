<template>
    <section v-if="server">
        <span id="cron-preview"></span>

        <div class="jcf-form-wrap">
            <form @submit.prevent="createServerCronJob" class="floating-labels">
                <input type="hidden" name="cron_timing">
                <div class="jcf-input-group">
                    <input type="text" name="cron" v-model="form.cron">
                    <label for="cron">
                        <span class="float-label">Name</span>
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

                <div class="jcf-input-group">
                    <div v-cronjob></div>
                </div>

                <div class="btn-footer">
                    <button class="btn btn-primary" type="submit">Create Cron Job</button>
                </div>
            </form>
        </div>

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
</template>

<script>
    export default {
        data() {
            return {
                form: {
                    cron: null,
                    user: 'root',
                    cron_timing : null,
                    server: this.$route.params.server_id,
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
                this.$store.dispatch('getServerCronJobs', this.$route.params.server_id);
            },
            createServerCronJob() {
                this.cron_timing = this.getCronTimings();
                this.$store.dispatch('createServerCronJob', this.form);
            },
            deleteCronJob(cron_job_id) {
                this.$store.dispatch('deleteServerCronJob', {
                    server: this.server.id,
                    cron_job: cron_job_id
                });
            },
            getCronTimings() {
                return $('input[name="cron_timing"]').val();
            }
        },
        computed: {
            server() {
                return this.$store.state.serversStore.server;
            },
            cron_jobs() {
                return this.$store.state.serverCronJobsStore.server_cron_jobs;
            }
        }
    }
</script>

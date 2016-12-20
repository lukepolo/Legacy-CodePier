<template>
    <div>
        <div class="jcf-form-wrap">
            <span id="cron-preview"></span>
            <form @submit.prevent="createSiteCronJob" class="floating-labels">
                <h3>Site Cron Jobs</h3>
                <input type="hidden" name="cron_timing" v-model="form.cron_timing">
                <div class="jcf-input-group">
                    <input type="text" name="cron" v-model="form.cron">
                    <label for="cron">
                        <span class="float-label">Cron Job</span>
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

                <div class="jcf-input-group">
                    <div class="select-wrap">
                        <div v-cronjob></div>
                    </div>
                </div>

                <div class="btn-footer">
                    <button class="btn btn-primary" type="submit">Create Cron</button>
                </div>

            </form>

            <table class="table" v-if="cronJobs" v-for="cronJob in cronJobs">
                <thead>
                <tr>
                    <th>Job</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>{{ cronJob.job }}</td>
                    <td>
                        <template v-if="isRunningCommandFor(cronJob.id)">
                            {{ isRunningCommandFor(cronJob.id).status }}
                        </template>
                        <a @click="deleteCronJob(cronJob.id)" href="#" class="fa fa-remove">X</a>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
    export default {
        props : ['site'],
        data() {
            return {
                form : {
                    user: 'root',
                    cron_timing: null,
                    cron: this.site.path ? this.site.path : null,
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
                this.$store.dispatch('getSiteCronJobs', this.$route.params.site_id);
            },
            createSiteCronJob() {
                this.$store.dispatch('createSiteCronJob', {
                    site : this.$route.params.site_id,
                    job : this.getCronTimings() + ' ' + this.job,
                    user : this.form.user
                });

                this.form = this.$options.data().form;
            },
            getCronTimings() {
                return $('input[name="cron_timing"]').val();
            },
            deleteCronJob(cronJobId) {
                this.$store.dispatch('deleteSiteCronJob', {
                    cron_job : cronJobId,
                    site : this.$route.params.site_id,
                });
            },
            isRunningCommandFor(id) {
                return this.isCommandRunning('App\\Models\\Site\\SiteCronJob', id);
            }
        },
        computed: {
            job() {
                return this.form.cron
            },
            cronJobs() {
                return this.$store.state.siteCronJobsStore.site_cron_jobs;
            }
        }
    }
</script>
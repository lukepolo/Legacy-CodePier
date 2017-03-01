<template>
    <div v-if="site">

        <div class="jcf-form-wrap">

            <form @submit.prevent="createSiteCronJob" class="floating-labels">
                <h3>Site Cron Jobs</h3>

                <input type="hidden" name="cron_timing" v-model="form.cron_timing">

                <div class="jcf-input-group">
                    <span class="input-group-addon">
                        <span id="cron-preview"></span>
                    </span>

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
                        <div id="cronjob-maker" v-cronjob></div>
                    </div>
                </div>

                <div class="btn-footer">
                    <button class="btn btn-primary" type="submit">Create Cron</button>
                </div>

            </form>

            <br>
            <table class="table" v-if="cronJobs">
                <thead>
                    <tr>
                        <th>Job</th>
                        <th>User</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="cronJob in cronJobs">
                        <td>{{ cronJob.job }}</td>
                        <td>{{ cronJob.user }}</td>
                        <td>
                            <template v-if="isRunningCommandFor(cronJob.id)">
                                {{ isRunningCommandFor(cronJob.id).status }}
                            </template>
                            <template v-else>
                                <a @click="deleteCronJob(cronJob.id)" href="#" class="fa fa-remove">X</a>
                            </template>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                form : {
                    cron: null,
                    user: 'root',
                    cron_timing: null
                }
            }
        },
        created() {
            this.fetchData()
        },
        watch: {
            '$route': 'fetchData'
        },
        methods: {
            fetchData() {
                this.$store.dispatch('getSiteCronJobs', this.$route.params.site_id)
            },
            createSiteCronJob() {
                if(this.getCronTimings()) {
                    this.$store.dispatch('createSiteCronJob', {
                        site: this.$route.params.site_id,
                        job: this.getCronTimings() + ' ' + this.form.cron,
                        user: this.form.user
                    }).then((cronJob) => {
                        if(cronJob) {
                            this.form.user = 'root';
                            this.form.cron_timing = null;
                            this.form.cron = this.site.path
                        }
                    })
                } else {
                    app.showError('You need to set a time for the cron to run.')
                }
            },
            getCronTimings() {
                return $('#cronjob-maker').cron('value')
            },
            deleteCronJob(cronJobId) {
                this.$store.dispatch('deleteSiteCronJob', {
                    cron_job : cronJobId,
                    site : this.$route.params.site_id,
                });
            },
            isRunningCommandFor(id) {
                return this.isCommandRunning('App\\Models\\CronJob', id)
            }
        },
        computed: {
            site() {
                let site = this.$store.state.sitesStore.site

                if(site) {
                    this.form.cron = site.path ? site.path : null
                }

                return site
            },
            cronJobs() {
                return this.$store.state.siteCronJobsStore.site_cron_jobs
            }
        }
    }
</script>
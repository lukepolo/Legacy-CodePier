<template>
    <section>

        <div class="jcf-form-wrap">

            <form @submit.prevent="createCronJob" class="floating-labels">

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

        <input type="hidden" v-if="site">

    </section>
</template>

<script>
    export default {
        data() {
            return {
                form : {
                    cron: null,
                    user: 'root',
                    cron_timing: null
                },
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

                if(this.siteId) {
                    this.$store.dispatch('user_site_cron_jobs/get', this.siteId)
                }

                if(this.serverId) {
                    this.$store.dispatch('user_server_cron_jobs/get', this.serverId)
                }
            },
            createCronJob() {

                if(this.getCronTimings()) {

                    let job = this.getCronTimings() + ' ' + this.form.cron

                    if(this.siteId) {
                        this.$store.dispatch('user_site_cron_jobs/store', {
                            job: job,
                            site: this.siteId,
                            user: this.form.user
                        }).then((cronJob) => {
                            if(cronJob) {
                                this.resetForm()
                            }
                        })
                    }

                    if(this.serverId) {
                        this.$store.dispatch('user_server_cron_jobs/store', {
                            job: job,
                            user: this.form.user,
                            server: this.serverId,
                        }).then((cronJob) => {
                            if(cronJob) {
                                this.resetForm()
                            }
                        })
                    }

                } else {
                    app.showError('You need to set a time for the cron to run.')
                }
            },
            getCronTimings() {
                return $('#cronjob-maker').cron('value')
            },
            deleteCronJob(cronJobId) {
                if(this.siteId) {
                    this.$store.dispatch('user_site_cron_jobs/destroy', {
                        site : this.siteId,
                        cron_job : cronJobId

                    });
                }

                if(this.serverId) {
                    this.$store.dispatch('user_server_cron_jobs/destroy', {
                        cron_job : cronJobId,
                        server : this.serverId
                    });
                }

            },
            isRunningCommandFor(id) {
                return this.isCommandRunning('App\\Models\\CronJob', id)
            },
            resetForm() {
                this.form.user = 'root';
                this.form.cron_timing = null;
                if(this.site) {
                    this.form.cron = this.site.path
                } else {
                    this.form.cron = null
                }
            }
        },
        computed: {
            site() {

                let site = this.$store.state.user_sites.site

                if(site) {
                    this.form.cron = site.path ? site.path : null
                }

                return site
            },
            siteId() {
                return this.$route.params.site_id
            },
            serverId() {
                return this.$route.params.server_id
            },
            cronJobs() {
                if(this.siteId) {
                    return this.$store.state.user_site_cron_jobs.cron_jobs
                }

                if(this.serverId) {
                    return this.$store.state.user_server_cron_jobs.cron_jobs
                }
            }
        }
    }
</script>
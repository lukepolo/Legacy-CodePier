<template>
    <section>
        <form @submit.prevent="createCronJob">
            <input type="hidden" name="cron_timing" v-model="form.cron_timing">

            <div class="flyform--group">
                <div class="flyform--group-prefix">
                    <input type="text" name="cron" v-model="form.cron" placeholder=" ">
                    <label for="repository">Cron Job</label>
                    <template v-if="!form.custom_provider">
                        <div class="flyform--group-prefix-label">
                            <span id="cron-preview"></span>
                        </div>
                    </template>
                </div>

            </div>

            <div class="flyform--group">
                <label>Select User</label>
                <div class="flyform--group-select">
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

            <server-selection :server_ids.sync="form.server_ids" :server_types.sync="form.server_types"></server-selection>

            <div class="flyform--footer">
                <div class="flyform--footer-btns">
                    <button class="btn btn-primary" type="submit">Create Cron Job</button>
                </div>
            </div>
        </form>

        <br>

        <div v-if="cronJobs.length">
            <h3>Cron Jobs</h3>

            <table class="table" v-if="cronJobs">
                <thead>
                <tr>
                    <th>Job</th>
                    <th>User</th>
                    <th></th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                    <cron-job :cronJob="cronJob" v-for="cronJob in cronJobs" :key="cronJob.id"></cron-job>
                </tbody>
            </table>
        </div>

        <input type="hidden" v-if="site">

    </section>
</template>

<script>

    import { ServerSelection, CronJob } from "./../components"

    export default {
        components : {
            CronJob,
            ServerSelection
        },
        data() {
            return {
                form : this.createForm({
                    cron: null,
                    user: 'root',
                    cron_timing: null,
                    server_ids : [],
                    server_types : [],
                }),
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
                            user: this.form.user,
                            server_ids : this.form.server_ids,
                            server_types : this.form.server_types,
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
                            server_ids : this.form.server_ids,
                            server_types : this.form.server_types,
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
            resetForm() {
                this.form.reset()
                this.form.user = 'root';
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
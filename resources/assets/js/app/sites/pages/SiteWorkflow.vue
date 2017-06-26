<template>
    <section>
        <h2 class="heading">
            Select the tasks needed to create your site.
        </h2>

        <form @submit.prevent="saveWorkflow" class="floating-labels">
            <div class="grid-2">
                <div class="grid-item">
                    <div class="flyform--group">
                        <label>Common Tasks Needed</label>
                    </div>

                    <div class="flyform--group-checkbox">
                        <label>
                            <input type="checkbox" v-model="form.workflow" name="workflow[]" value="site_databases">
                            <span class="icon"></span>
                            Create Databases

                            <tooltip message="" size="medium">
                                <span class="fa fa-info-circle"></span>
                            </tooltip>
                        </label>
                    </div>

                    <div class="flyform--group-checkbox">
                        <label>
                            <input type="checkbox" v-model="form.workflow" name="workflow[]" value="site_workers">
                            <span class="icon"></span>
                            Set up Workers

                            <tooltip message="" size="medium">
                                <span class="fa fa-info-circle"></span>
                            </tooltip>
                        </label>
                    </div>

                    <div class="flyform--group-checkbox">
                        <label>
                            <input type="checkbox" v-model="form.workflow" name="workflow[]" value="site_cron_jobs">
                            <span class="icon"></span>
                            Set up Cron Jobs

                            <tooltip message="" size="medium">
                                <span class="fa fa-info-circle"></span>
                            </tooltip>
                        </label>
                    </div>

                    <div class="flyform--group-checkbox">
                        <label>
                            <input type="checkbox" v-model="form.workflow" name="workflow[]" value="site_files">
                            <span class="icon"></span>
                            Update Your Site Files

                            <tooltip message="" size="medium">
                                <span class="fa fa-info-circle"></span>
                            </tooltip>
                        </label>
                    </div>

                    <div class="flyform--group-checkbox">
                        <label>
                            <input type="checkbox" v-model="form.workflow" name="workflow[]" value="site_ssl_certs">
                            <span class="icon"></span>
                            Set up SSL Certificates

                            <tooltip message="You can install a free Lets Encrypt certificate or include your own" size="medium">
                                <span class="fa fa-info-circle"></span>
                            </tooltip>
                        </label>
                    </div>
                </div>


                <div class="grid-item">
                    <div class="flyform--group">
                        <label>Uncommon Tasks Needed</label>
                    </div>

                    <div class="flyform--group-checkbox">
                        <label>
                            <input type="checkbox" v-model="form.workflow" name="workflow[]" value="site_firewall_rules">
                            <span class="icon"></span>
                            Open Firewall Ports

                            <tooltip message="Except for ports 80 and 443" size="medium">
                                <span class="fa fa-info-circle"></span>
                            </tooltip>
                        </label>
                    </div>

                    <div class="flyform--group-checkbox">
                        <label>
                            <input type="checkbox" v-model="form.workflow" name="workflow[]" value="site_ssh_keys">
                            <span class="icon"></span>
                            Set up Additional SSH Keys

                            <tooltip message="These are keys that are not already in your account" size="medium">
                                <span class="fa fa-info-circle"></span>
                            </tooltip>
                        </label>
                    </div>

                    <div class="flyform--group-checkbox">
                        <label>
                            <input type="checkbox" v-model="form.workflow" name="workflow[]" value="site_environment_variables">
                            <span class="icon"></span>
                            Set up Environment Variables

                            <tooltip message="" size="medium">
                                <span class="fa fa-info-circle"></span>
                            </tooltip>
                        </label>
                    </div>

                    <div class="flyform--group-checkbox">
                        <label>
                            <input type="checkbox" v-model="form.workflow" name="workflow[]" value="site_server_features">
                            <span class="icon"></span>
                            Additional Software for Your Server

                            <tooltip message="Your site has already been customized based on your repository" size="medium">
                                <span class="fa fa-info-circle"></span>
                            </tooltip>

                            <br>
                            <small>Advanced Users Only</small>
                        </label>
                    </div>


                </div>
            </div>

            <div class="flyform--footer">
                <div class="flyform--footer-btns">
                    <button @click.prevent="skipWorkflow" class="btn btn-danger" type="submit">Skip</button>
                    <button class="btn btn-primary" type="submit">Continue</button>
                </div>

                <div class="flyform--footer-links">
                    <small>You can opt out of using the workflow in your <router-link :to="{ name: 'my_account' }">profile</router-link>.</small>
                </div>
            </div>

        </form>

    </section>
</template>

<script>
    export default {
        data() {
            return {
                form : {
                    workflow: [
                        'site_databases',
                        'site_workers',
                        'site_cron_jobs',
                        'site_files',
                        'site_ssl_certs'
                    ]
                }
            }
        },
        methods: {
            skipWorkflow() {
                this.$store.dispatch('user_sites/updateWorkflow', {
                    workflow : {},
                    site : this.$route.params.site_id,
                })
            },
            saveWorkflow() {
                this.$store.dispatch('user_sites/updateWorkflow', {
                    workflow : _.mapValues(_.invert(this.form.workflow), function() {
                        return false
                    }),
                    site : this.$route.params.site_id,
                })
            }

        },
        computed: {
            site() {
                return this.$store.state.user_sites.site;
            }
        },
    }
</script>
<template>
    <section>
        <h3>
            Now that you have entered in your information we will build you a workflow to make it easy as possible to make your application come to life.
        </h3>

        <div class="jcf-form-wrap">

            <form @submit.prevent="saveWorkflow" class="floating-labels">

                <div class="jcf-input-group input-checkbox">

                    <div class="input-question">Normal Something not sure what to call it</div>

                    <label>

                        <tooltip message="" size="medium">
                            <span class="fa fa-info-circle"></span>
                        </tooltip>

                        <input type="checkbox" v-model="form.workflow" name="workflow[]" value="site_files">
                        <span class="icon"></span>
                        Do you need to update your site files?
                    </label>
                </div>

                <div class="jcf-input-group input-checkbox">
                    <label>

                        <tooltip message="" size="medium">
                            <span class="fa fa-info-circle"></span>
                        </tooltip>

                        <input type="checkbox" v-model="form.workflow" name="workflow[]" value="site_databases">
                        <span class="icon"></span>
                        Do you need a database?

                    </label>
                </div>

                <div class="jcf-input-group input-checkbox">
                    <label>

                        <tooltip message="These are keys that are not already in your account" size="medium">
                            <span class="fa fa-info-circle"></span>
                        </tooltip>

                        <input type="checkbox" v-model="form.workflow" name="workflow[]" value="site_ssh_keys">
                        <span class="icon"></span>
                        Do you need to setup additional ssh keys?

                    </label>
                </div>

                <div class="jcf-input-group input-checkbox">
                    <label>

                        <tooltip message="You can install a free Lets Encrypt certificate or include your own" size="medium">
                            <span class="fa fa-info-circle"></span>
                        </tooltip>

                        <input type="checkbox" v-model="form.workflow" name="workflow[]" value="site_ssl_certs">
                        <span class="icon"></span>
                        Do you need to setup SSL certificates?

                    </label>
                </div>

                <div class="jcf-input-group input-checkbox">
                    <label>

                        <tooltip message="" size="medium">
                            <span class="fa fa-info-circle"></span>
                        </tooltip>

                        <input type="checkbox" v-model="form.workflow" name="workflow[]" value="site_workers">
                        <span class="icon"></span>
                        Do you need to setup workers?

                    </label>
                </div>

                <div class="jcf-input-group input-checkbox">
                    <label>

                        <tooltip message="" size="medium">
                            <span class="fa fa-info-circle"></span>
                        </tooltip>

                        <input type="checkbox" v-model="form.workflow" name="workflow[]" value="site_cron_jobs">
                        <span class="icon"></span>
                        Do you need to setup cron jobs?

                    </label>
                </div>

                <div class="jcf-input-group input-checkbox">
                    <label>

                        <tooltip message="Except for ports 80 and 443" size="medium">
                            <span class="fa fa-info-circle"></span>
                        </tooltip>

                        <input type="checkbox" v-model="form.workflow" name="workflow[]" value="site_firewall_rules">
                        <span class="icon"></span>
                        Do you need to open firewall ports ?

                    </label>
                </div>

                <div class="jcf-input-group input-checkbox">
                    <label>

                        <tooltip message="" size="medium">
                            <span class="fa fa-info-circle"></span>
                        </tooltip>

                        <input type="checkbox" v-model="form.workflow" name="workflow[]" value="site_environment_variables">
                        <span class="icon"></span>
                        Do you need to setup environment variables?

                    </label>
                </div>

                <div class="jcf-input-group input-checkbox">
                    <label>

                        <tooltip message="Your site has already been customized based on your repository" size="medium">
                            <span class="fa fa-info-circle"></span>
                        </tooltip>

                        <input type="checkbox" v-model="form.workflow" name="workflow[]" value="site_server_features">
                        <span class="icon"></span>
                        Do you need additional software for your server? (Advanced Users Only)

                    </label>
                </div>

                <div class="btn-footer">
                    <button class="btn btn-primary" type="submit" :disabled="form.diff().length === 0">Update Workflow</button>
                </div>

            </form>
        </div>
    </section>
</template>

<script>
    export default {
        data() {
            return {
                form : this.createForm({
                    workflow : [
                        'site_files',
                        'site_databases',
                        'site_ssl_certs'
                    ]
                })
            }
        },
        methods: {
            saveWorkflow() {
                alert('save dat workflow!')
            }

        },
        computed: {
            site() {
                return this.$store.state.user_sites.site;
            }
        },
    }
</script>
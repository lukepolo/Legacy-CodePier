<template>
    <section>
        <h2 class="heading">
            Select the tasks needed to create your site.
        </h2>

        <form @submit.prevent="saveWorkflow">
            <div class="grid-2">
                <div class="grid-item">
                    <template v-for="(workflow, index) in workflows" v-if="index <= 5">
                        <div class="flyform--group-checkbox">
                            <label>
                                <input type="checkbox" v-model="form.workflow" name="workflow[]" :value="workflow">
                                <span class="icon"></span>
                                {{ workflow.description }}

                                <tooltip :message="workflow.tooltip" size="medium" v-if="workflow.tooltip">
                                    <span class="fa fa-info-circle"></span>
                                </tooltip>

                                <template v-if="workflow.subtext">
                                    <br>
                                    <small>{{ workflow.subtext }}</small>
                                </template>
                            </label>
                        </div>
                    </template>
                </div>
                <div class="grid-item">
                    <template v-for="(workflow, index) in workflows" v-if="index > 5">
                        <div class="flyform--group-checkbox">
                            <label>
                                <input type="checkbox" v-model="form.workflow" name="workflow[]" :value="workflow">
                                <span class="icon"></span>
                                {{ workflow.description }}

                                <tooltip :message="workflow.tooltip" size="medium" v-if="workflow.tooltip">
                                    <span class="fa fa-info-circle"></span>
                                </tooltip>

                                <template v-if="workflow.subtext">
                                    <br>
                                    <small>{{ workflow.subtext }}</small>
                                </template>
                            </label>
                        </div>
                    </template>
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
      workflows: [
        {
          order: 2,
          name: "site_deployment",
          description: "Setup Site Deployment"
        },
        {
          order: 3,
          name: "site_databases",
          description: "Create Databases"
        },
        {
          order: 4,
          name: "site_files",
          description: "Update Your Site Files"
        },
        {
          order: 5,
          name: "site_ssl_certs",
          description: "Set up SSL Certificates",
          tooltip:
            "You can install a free Lets Encrypt certificate or include your own"
        },
        {
          order: 6,
          name: "site_workers",
          description: "Set up Workers"
        },
        {
          order: 7,
          name: "site_daemons",
          description: "Set up Daemons"
        },
        {
          order: 8,
          name: "site_cron_jobs",
          description: "Set up Cron Jobs"
        },
        {
          order: 9,
          name: "site_firewall_rules",
          description: "Open Firewall Ports",
          tooltip: "Ports 22 / 80 / 443 Are opened by default"
        },
        {
          order: 10,
          name: "site_ssh_keys",
          description: "Set up Additional SSH Keys",
          tooltip: "Add keys that are not already in your account"
        },
        {
          order: 11,
          name: "site_environment_variables",
          description: "Set up Environment Variables"
        },
        {
          order: 1,
          name: "site_server_features",
          description: "Change Server Software",
          tooltip:
            "Your site has already been customized based on your repository",
          subtext: "Advanced Users should review the default software"
        }
      ],
      form: this.createForm({
        workflow: []
      })
    };
  },
  created() {
    Vue.set(this.form, "workflow", [
      this.workflows[0],
      this.workflows[1],
      this.workflows[2],
      this.workflows[3]
    ]);
  },
  methods: {
    skipWorkflow() {
      this.$store.dispatch("user_sites/updateWorkflow", {
        workflow: {},
        site: this.$route.params.site_id
      });
    },
    saveWorkflow() {
      let workflow = _.invert(
        _.map(_.orderBy(this.form.workflow, "order"), "name")
      );
      this.$store.dispatch("user_sites/updateWorkflow", {
        workflow: _.mapValues(workflow, function() {
          return false;
        }),
        site: this.$route.params.site_id
      });
    }
  },
  computed: {
    site() {
      return this.$store.state.user_sites.site;
    }
  }
};
</script>

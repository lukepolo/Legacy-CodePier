<template>
    <base-form :action="saveWorkflow">
        <div class="flex">
            <div class="flex--grow">
                <h2>Select the tasks needed to create your site.</h2>
            </div>

            <div class="heading--btns">
                <!--<delete-site :site="site"></delete-site>-->
            </div>
        </div>

        <hr>
        <div class="grid-2">
            <div class="grid-item">
                <template v-for="workflow in workflows">
                    <base-checkbox
                        :label="workflow.description"
                        :name="workflow.name"
                        v-model="form.workflow"
                        :value="workflow"
                        :tooltip="workflow.tooltip"
                        :description="workflow.subtext"
                    ></base-checkbox>
                </template>
            </div>
        </div>

        <template slot="buttons">
            <button @click.prevent="skipWorkflow" class="btn btn-danger" type="submit">Skip</button>
            <button class="btn btn-primary" type="submit">Continue</button>
        </template>

        <template slot="links">
            <small>You can opt out of using the workflow in your <router-link :to="{ name: 'user.account' }">profile</router-link>.</small>
        </template>
    </base-form>
</template>

<script>
// import DeleteSite from "./../components/DeleteSite";

export default {
  components: {
    // DeleteSite,
  },
  data() {
    return {
      workflows: [
        {
          order: 2,
          name: "site.deployment",
          description: "Set up Site Deployment",
        },
        {
          order: 3,
          name: "site.databases",
          description: "Create Databases",
        },
        {
          order: 4,
          name: "site.files",
          description: "Update Your Site Files",
        },
        {
          order: 5,
          name: "site.ssl-certs",
          description: "Set up SSL Certificates",
          tooltip:
            "You can install a free Lets Encrypt certificate or include your own",
        },
        {
          order: 6,
          name: "site.workers",
          description: "Set up Workers",
        },
        {
          order: 7,
          name: "site.daemons",
          description: "Set up Daemons",
        },
        {
          order: 8,
          name: "site.cron-jobs",
          description: "Set up Cron Jobs",
        },
        {
          order: 9,
          name: "site.firewall-rules",
          description: "Open Firewall Ports",
          tooltip: "Ports 22 / 80 / 443 Are opened by default",
        },
        {
          order: 10,
          name: "site.ssh-keys",
          description: "Set up Additional SSH Keys",
          tooltip: "Add keys that are not already in your account",
        },
        {
          order: 11,
          name: "site.environment-variables",
          description: "Set up Environment Variables",
        },
        {
          order: 1,
          name: "site.server-features",
          description: "Change Server Software",
          tooltip:
            "Your site has already been customized based on your repository",
          subtext: "Advanced Users should review the default software",
        },
      ],
      form: this.createForm({
        test: false,
        another: true,
        workflow: [],
      }),
    };
  },
  created() {
    this.form
      .reset()
      .fill({
        workflow: [
          this.workflows[0],
          this.workflows[1],
          this.workflows[2],
          this.workflows[3],
        ],
      })
      .setAsOriginalData();

    // {
    //   "site_files": {
    //   "step": "site_files",
    //     "order": 3,
    //     "completed": true
    // },
    //   "site_workers": {
    //   "step": "site_workers",
    //     "order": 4,
    //     "completed": true
    // },
    //   "site_cron_jobs": {
    //   "step": "site_cron_jobs",
    //     "order": 5,
    //     "completed": true
    // },
    //   "site_databases": {
    //   "step": "site_databases",
    //     "order": 2,
    //     "completed": true
    // },
    //   "site_deployment": {
    //   "step": "site_deployment",
    //     "order": 1,
    //     "completed": true
    // },
    //   "site_environment_variables": {
    //   "step": "site_environment_variables",
    //     "order": 6,
    //     "completed": true
    // }
    // }
  },
  methods: {
    skipWorkflow() {
      // this.$store.dispatch("user_sites/updateWorkflow", {
      //   workflow: {},
      //   site: this.$route.params.site_id,
      // });
    },
    saveWorkflow() {
      this.$store.dispatch("user/sites/updateWorkflow", {
        site: this.$route.params.site,
        workflow: this.form.data().workflow.map((workflow) => {
          return {
            step: workflow.name,
            order: workflow.order,
          };
        }),
      });
    },
  },
  computed: {
    site() {
      return this.$store.state.user_sites.site;
    },
  },
};
</script>

<template>
    <section>

        <div class="flex flex--center">
            <h3 class="flex--grow">
                Cron Jobs
            </h3>

            <tooltip message="Add Cron Job">
                <span class="btn btn-small btn-primary" :class="{ 'btn-disabled' : this.shouldShowForm }" @click="showForm = true">
                    <span class="icon-plus"></span>
                </span>
            </tooltip>
        </div>

        <table class="table" v-if="cronJobs.length">
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

        <form @submit.prevent="createCronJob" v-if="shouldShowForm">
            <div class="flyform--group">
                <div class="flyform--group-prefix">
                    {{ this.form.cronTiming }}
                    <input type="text" name="cron" v-model="form.cron" placeholder=" ">
                    <label>Cron Job</label>
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

            <cron-job-maker :cronTiming.sync="form.cronTiming"></cron-job-maker>

            <server-selection
                title="New Cron Job"
                :server_ids.sync="form.server_ids"
                :server_types.sync="form.server_types"
                :showFormGroup="true"
            ></server-selection>

            <div class="flyform--footer">
                <div class="flyform--footer-btns">
                    <button class="btn" v-if="cronJobs.length" @click.prevent="resetForm">Cancel</button>
                    <button class="btn btn-primary" type="submit">Create Cron Job</button>
                </div>
            </div>
        </form>

        <input type="hidden" v-if="site">

    </section>
</template>

<script>
import { ServerSelection, CronJob, CronJobMaker } from "./../components";

export default {
  components: {
    CronJob,
    CronJobMaker,
    ServerSelection
  },
  data() {
    return {
      loaded: false,
      showForm: false,
      form: this.createForm({
        cron: "",
        user: "root",
        cronTiming: "* * * * *",
        server_ids: [],
        server_types: []
      })
    };
  },
  created() {
    this.fetchData();
  },
  watch: {
    $route: "fetchData"
  },
  methods: {
    fetchData() {
      if (this.siteId) {
        this.$store
          .dispatch("user_site_cron_jobs/get", this.siteId)
          .then(() => {
            this.loaded = true;
          });
      }

      if (this.serverId) {
        this.$store
          .dispatch("user_server_cron_jobs/get", this.serverId)
          .then(() => {
            this.loaded = true;
          });
      }
    },
    createCronJob() {
      let job = this.form.cronTiming + " " + this.form.cron;

      if (this.siteId) {
        this.$store
          .dispatch("user_site_cron_jobs/store", {
            job: job,
            site: this.siteId,
            user: this.form.user,
            server_ids: this.form.server_ids,
            server_types: this.form.server_types
          })
          .then(cronJob => {
            if (cronJob) {
              this.resetForm();
            }
          });
      }

      if (this.serverId) {
        this.$store
          .dispatch("user_server_cron_jobs/store", {
            job: job,
            user: this.form.user,
            server: this.serverId,
            server_ids: this.form.server_ids,
            server_types: this.form.server_types
          })
          .then(cronJob => {
            if (cronJob) {
              this.resetForm();
            }
          });
      }
    },
    resetForm() {
      this.form.reset();
      this.form.user = "root";
      if (this.site) {
        this.form.cron = this.site.path;
      } else {
        this.form.cron = "";
      }
      this.showForm = false;
    }
  },
  computed: {
    site() {
      let site = this.$store.state.user_sites.site;

      if (site) {
        this.form.cron = site.path ? site.path : null;
      }

      return site;
    },
    siteId() {
      return this.$route.params.site_id;
    },
    serverId() {
      return this.$route.params.server_id;
    },
    cronJobs() {
      if (this.siteId) {
        return this.$store.state.user_site_cron_jobs.cron_jobs;
      }

      if (this.serverId) {
        return this.$store.state.user_server_cron_jobs.cron_jobs;
      }
    },
    shouldShowForm() {
      return (this.loaded && this.cronJobs.length === 0) || this.showForm;
    }
  }
};
</script>

<template>
  <section>
    <div class="flex flex--center">
      <h3 class="flex--grow">Cron Jobs</h3>

      <tooltip message="Add Cron Job">
        <span
          class="btn btn-small btn-primary"
          :class="{ 'btn-disabled': this.showForm === true }"
          @click="showForm = true"
        >
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
        <tr v-for="cronJob in cronJobs">
          <cron-job :cron-job="cronJob"></cron-job>
        </tr>
      </tbody>
    </table>

    <base-form v-form="form" :action="createCronJob" v-if="showForm">
      <base-input label="Cron Job" v-model="form.cron" name="cron">
        <template slot="prefix">
          {{ form.cronTiming }}
        </template>
      </base-input>

      <select v-model="form.user" name="user" label="User to run command(s)">
        <option value="root">Root</option>
        <option value="codepier">CodePier</option>
      </select>

      <cron-job-maker v-model="form.cronTiming"></cron-job-maker>

      <template slot="buttons">
        <span class="btn" @click.prevent="cancel">Cancel</span>
        <button
          class="btn btn-primary"
          :disabled="!form.isValid()"
          type="submit"
        >
          Add SSH Key
        </button>
      </template>
    </base-form>
  </section>
</template>

<script>
import CronJob from "./components/CronJob";
import CronJobMaker from "./components/CronJobMaker";
export default {
  components: {
    CronJob,
    CronJobMaker,
  },
  data() {
    return {
      showForm: false,
      form: this.createForm({
        cron: null,
        user: null,
        cronTiming: null,
      }).validation({
        rules: {
          cron: "required|max:255",
          user: "required",
          cronTiming: "required", // TODO - validation both front and backend
        },
      }),
    };
  },
  watch: {
    $route: {
      immediate: true,
      handler: "fetchData",
    },
    cronJobs: {
      handler() {
        if (!this.cronJobs.length) {
          this.showForm = true;
        }
      },
    },
  },
  methods: {
    fetchData() {
      this.$store.dispatch("user/sites/cronJobs/get", {
        site: this.$route.params.site,
      });
    },
    createCronJob() {
      let cronJobData = this.form.data();

      cronJobData.job = `${cronJobData.cronTiming} ${cronJobData.cron}`;

      this.$store
        .dispatch("user/sites/cronJobs/create", {
          data: cronJobData,
          parameters: {
            site: this.$route.params.site,
          },
        })
        .then(() => {
          this.cancel();
        });
    },
    cancel() {
      this.form.reset();
      this.showForm = false;
    },
  },
  computed: {
    cronJobs() {
      return this.$store.state.user.sites.cronJobs.cron_jobs;
    },
  },
};
</script>

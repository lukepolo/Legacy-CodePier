<template>
  <tr>
    <td class="break-word">{{ cronJob.job }}</td>
    <td>{{ cronJob.user }}</td>
    <td>
      <!--<template v-if="isRunningCommand">-->
      <!--{{ isRunningCommand.status }}-->
      <!--</template>-->
    </td>

    <td class="table--action">
      <!--<server-selection-->
      <!--:title="cronJob.job"-->
      <!--:update="updateCronJob(cronJob)"-->
      <!--:server_ids.sync="form.server_ids"-->
      <!--:server_types.sync="form.server_types"-->
      <!--&gt;</server-selection>-->

      <tooltip message="Delete">
        <span class="table--action-delete">
          <a @click="deleteCronJob"><span class="icon-trash"></span></a>
        </span>
      </tooltip>
    </td>
  </tr>
</template>

<script>
// import ServerSelection from "./ServerSelection";

export default {
  props: ["cronJob"],
  components: {
    // ServerSelection
  },
  data() {
    return {
      form: this.createForm({
        cron_job: this.cronJob.id,
        site: this.$route.params.site_id,
        server_ids: this.cronJob.server_ids,
        server_types: this.cronJob.server_types,
      }),
    };
  },
  methods: {
    updateCronJob() {
      return () => {
        return this.$store.dispatch("user_site_cron_jobs/patch", this.form);
      };
    },
    deleteCronJob() {
      this.$store.dispatch("user/sites/cronJobs/destroy", {
        cron_job: this.cronJob.id,
        site: this.$route.params.site,
      });
    },
  },
};
</script>

<template>
  <tr>
    <td class="break-word">{{ worker.command }}</td>
    <td>{{ worker.user }}</td>
    <td>{{ worker.auto_start }}</td>
    <td>{{ worker.auto_restart }}</td>
    <td>{{ worker.number_of_workers }}</td>
    <td>
      <!--<template v-if="isRunningCommandFor(worker.id)">-->
      <!--{{ isRunningCommandFor(worker.id).status }}-->
      <!--</template>-->
    </td>

    <td class="table--action">
      <tooltip message="Delete">
        <span class="table--action-delete">
          <a @click="deleteWorker(worker.id)"
            ><span class="icon-trash"></span
          ></a>
        </span>
      </tooltip>
    </td>
  </tr>
</template>

<script>
// import ServerSelection from "./ServerSelection";

export default {
  props: ["worker"],
  components: {
    // ServerSelection
  },
  methods: {
    deleteWorker() {
      this.$store.dispatch("user/sites/workers/destroy", {
        worker: this.worker.id,
        site: this.$route.params.site,
      });
    },
  },
  computed: {
    siteId() {
      return this.$route.params.site_id;
    },
    serverId() {
      return this.$route.params.server_id;
    },
    isRunningCommand() {
      // return this.isCommandRunning("App\\Models\\Worker", this.worker.id);
    },
  },
};
</script>

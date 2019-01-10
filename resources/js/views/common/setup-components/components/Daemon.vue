<template>
  <tr>
    <td class="break-word">{{ daemon.command }}</td>
    <td>{{ daemon.user }}</td>
    <td>
      <!--<template v-if="isRunningCommand">-->
      <!--{{ isRunningCommand.status }}-->
      <!--</template>-->
    </td>

    <td class="table--action">
      <!--<server-selection-->
      <!--:title="daemon.command"-->
      <!--:update="updateDaemon(daemon)"-->
      <!--:server_ids.sync="form.server_ids"-->
      <!--:server_types.sync="form.server_types"-->
      <!--&gt;</server-selection>-->

      <tooltip message="Delete">
        <span class="table--action-delete">
          <a @click="deleteDaemon"><span class="icon-trash"></span></a>
        </span>
      </tooltip>
    </td>
  </tr>
</template>

<script>
// import ServerSelection from "./ServerSelection";

export default {
  props: ["daemon"],
  components: {
    // ServerSelection
  },
  data() {
    return {
      form: this.createForm().fill({
        daemon: this.daemon.id,
        site: this.$route.params.site_id,
        server_ids: this.daemon.server_ids,
        server_types: this.daemon.server_types,
      }),
    };
  },
  methods: {
    updateDaemon() {
      return () => {
        return this.$store.dispatch("user_site_daemons/patch", this.form);
      };
    },
    deleteDaemon() {
      this.$store.dispatch("user/sites/daemons/destroy", {
        daemon: this.daemon.id,
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
      // return this.isCommandRunning("App\\Models\\Daemon", this.daemon.id);
    },
  },
};
</script>

<template>
    <section>
        <div class="flex flex--center">
            <h3 class="flex--grow">
                Daemons
            </h3>

            <tooltip message="Add Daemon">
                <span class="btn btn-small btn-primary" :class="{ 'btn-disabled' : this.shouldShowForm }" @click="showForm = true">
                    <span class="icon-plus"></span>
                </span>
            </tooltip>
        </div>
        <table class="table" v-if="daemons.length">
            <thead>
            <tr>
                <th>Command</th>
                <th>User</th>
                <th></th>
                <th></th>
            </tr>
            </thead>
            <tbody>
                <daemon :daemon="daemon" v-for="daemon in daemons" :key="daemon.id"></daemon>
            </tbody>
        </table>

        <form @submit.prevent="installDaemon()" v-if="shouldShowForm">

            <div class="flyform--group">
                <input type="text" name="command" v-model="form.command" placeholder=" ">
                <label for="command">Command</label>
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

            <server-selection
                title="New Daemon"
                :server_ids.sync="form.server_ids"
                :server_types.sync="form.server_types"
                :showFormGroup="true"
            ></server-selection>

            <div class="flyform--footer">
                <div class="flyform--footer-btns">
                    <button class="btn" v-if="daemons.length" @click.prevent="resetForm">Cancel</button>
                    <button class="btn btn-primary" type="submit">Create Daemon</button>
                </div>
            </div>

        </form>

        <input type="hidden" v-if="site">

    </section>
</template>

<script>
import { ServerSelection, Daemon } from "./../components";
export default {
  components: {
    Daemon,
    ServerSelection
  },
  data() {
    return {
      loaded: false,
      showForm: false,
      form: this.createForm({
        user: null,
        command: null,
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
        this.$store.dispatch("user_site_daemons/get", this.siteId).then(() => {
          this.loaded = true;
        });
      }

      if (this.serverId) {
        this.$store
          .dispatch("user_server_daemons/get", this.serverId)
          .then(() => {
            this.loaded = true;
          });
      }
    },
    installDaemon() {
      if (this.siteId) {
        this.form.site = this.siteId;
        this.$store
          .dispatch("user_site_daemons/store", this.form)
          .then(daemon => {
            if (daemon.id) {
              this.resetForm();
            }
          });
      }

      if (this.serverId) {
        this.form.server = this.serverId;
        this.$store
          .dispatch("user_server_daemons/store", this.form)
          .then(daemon => {
            if (daemon.id) {
              this.resetForm();
            }
          });
      }
    },
    isRunningCommandFor(id) {
      return this.isCommandRunning("App\\Models\\Daemon", id);
    },
    resetForm() {
      this.form.reset();
      if (this.site) {
        this.form.command = this.site.path;
      }
      this.showForm = false;
    }
  },
  computed: {
    site() {
      let site = this.$store.state.user_sites.site;

      if (site) {
        this.form.command =
          site.path + (site.zero_downtime_deployment ? "/current/" : "/");
      }

      return site;
    },
    siteId() {
      return this.$route.params.site_id;
    },
    serverId() {
      return this.$route.params.server_id;
    },
    daemons() {
      if (this.siteId) {
        return this.$store.state.user_site_daemons.daemons;
      }

      if (this.serverId) {
        return this.$store.state.user_server_daemons.daemons;
      }
    },
    shouldShowForm() {
      return (this.loaded && this.daemons.length === 0) || this.showForm;
    }
  }
};
</script>

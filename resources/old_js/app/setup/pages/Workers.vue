<template>
    <section>

        <div class="flex flex--center">
            <h3 class="flex--grow">
                Workers
            </h3>

            <tooltip message="Add Worker">
                <span class="btn btn-small btn-primary" :class="{ 'btn-disabled' : this.shouldShowForm }" @click="showForm = true">
                    <span class="icon-plus"></span>
                </span>
            </tooltip>
        </div>

        <table class="table" v-if="workers.length">
            <thead>
            <tr>
                <th>Command</th>
                <th>User</th>
                <th>Auto Start</th>
                <th>Auto Restart</th>
                <th># of Workers</th>
                <th></th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="worker in workers">
                <td class="text-break">{{ worker.command }}</td>
                <td>{{ worker.user }}</td>
                <td>{{ worker.auto_start }}</td>
                <td>{{ worker.auto_restart }}</td>
                <td>{{ worker.number_of_workers }}</td>
                <td>
                    <template v-if="isRunningCommandFor(worker.id)">
                        {{ isRunningCommandFor(worker.id).status }}
                    </template>
                </td>

                <td class="table--action">
                    <tooltip message="Delete">
                            <span class="table--action-delete">
                                <a @click="deleteWorker(worker.id)"><span class="icon-trash"></span></a>
                            </span>
                    </tooltip>
                </td>
            </tr>
            </tbody>
        </table>

        <form @submit.prevent="installWorker()" v-if="shouldShowForm">

            <div class="flyform--group">
                <input type="text" name="command" v-model="form.command" placeholder=" ">
                <label for="command">Command</label>
            </div>

            <div class="flyform--group">
                <tooltip message="The directory that the process will run in">
                    <span class="icon-info"></span>
                </tooltip>
                <label class="flyform--group-iconlabel" for="working_directory">Working Directory</label>
                <input type="text" name="working_directory" v-model="form.working_directory" placeholder="optional">
            </div>

            <div class="flyform--group">
                <input type="integer" name="number_of_workers" v-model="form.number_of_workers" placeholder=" ">
                <label for="number_of_workers">Number of Workers</label>
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

            <div class="flyform--group">
                <label>Worker Options</label>
            </div>
            <div class="flyform--group-checkbox">
                <label>
                    <input type="checkbox" name="auto_start" v-model="form.auto_start">
                    <span class="icon"></span>
                    Auto Start
                </label>
            </div>

            <div class="flyform--group-checkbox">
                <label>
                    <input type="checkbox" name="auto_restart" v-model="form.auto_restart">
                    <span class="icon"></span>
                    Auto Restart
                </label>
            </div>

            <div class="flyform--footer">
                <div class="flyform--footer-btns">
                    <span class="btn" v-if="workers.length" @click.prevent="resetForm">Cancel</span>
                    <button class="btn btn-primary" type="submit">Create Queue Worker</button>
                </div>
            </div>

        </form>

        <input type="hidden" v-if="site">

    </section>
</template>

<script>
export default {
  data() {
    return {
      loaded: false,
      showForm: false,
      form: this.createForm({
        user: null,
        command: null,
        auto_start: true,
        auto_restart: true,
        number_of_workers: 1,
        working_directory: null,
      }),
    };
  },
  created() {
    this.fetchData();
  },
  watch: {
    $route: "fetchData",
  },
  methods: {
    fetchData() {
      if (this.siteId) {
        this.$store.dispatch("user_site_workers/get", this.siteId).then(() => {
          this.loaded = true;
        });
      }

      if (this.serverId) {
        this.$store
          .dispatch("user_server_workers/get", this.serverId)
          .then(() => {
            this.loaded = true;
          });
      }
    },
    installWorker() {
      if (this.siteId) {
        this.form.site = this.siteId;
        this.$store
          .dispatch("user_site_workers/store", this.form)
          .then((worker) => {
            if (worker.id) {
              this.resetForm();
            }
          });
      }

      if (this.serverId) {
        this.form.server = this.serverId;
        this.$store
          .dispatch("user_server_workers/store", this.form)
          .then((worker) => {
            if (worker.id) {
              this.resetForm();
            }
          });
      }
    },
    deleteWorker: function(worker_id) {
      if (this.siteId) {
        this.$store.dispatch("user_site_workers/destroy", {
          worker: worker_id,
          site: this.siteId,
        });
      }

      if (this.serverId) {
        this.$store.dispatch("user_server_workers/destroy", {
          worker: worker_id,
          server: this.serverId,
        });
      }
    },
    isRunningCommandFor(id) {
      return this.isCommandRunning("App\\Models\\Worker", id);
    },
    resetForm() {
      this.form.reset();
      if (this.site) {
        this.form.command =
          this.site.path +
          (this.site.zero_downtime_deployment ? "/current/" : "/");
      }
      this.showForm = false;
    },
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
    workers() {
      if (this.siteId) {
        return this.$store.state.user_site_workers.workers;
      }

      if (this.serverId) {
        return this.$store.state.user_server_workers.workers;
      }
    },
    shouldShowForm() {
      return (this.loaded && this.workers.length === 0) || this.showForm;
    },
  },
};
</script>

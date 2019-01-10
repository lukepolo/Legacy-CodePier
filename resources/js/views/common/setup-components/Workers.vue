<template>
  <section>
    <div class="flex flex--center">
      <h3 class="flex--grow">Workers</h3>

      <tooltip message="Add Worker">
        <span
          class="btn btn-small btn-primary"
          :class="{ 'btn-disabled': this.showForm === true }"
          @click="showForm = true"
        >
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
          <worker :worker="worker"></worker>
        </tr>
      </tbody>
    </table>

    <base-form v-form="form" :action="createWorker" v-if="showForm">
      <base-input
        validate
        v-model="form.command"
        label="Command"
        name="command"
      ></base-input>

      <base-input
        validate
        v-model="form.working_directory"
        label="Working Directory"
        name="working_directory"
        tooltip="The directory that the process will run in"
      ></base-input>

      <select v-model="form.user" name="user" label="User to run worker">
        <option value="root">Root</option>
        <option value="codepier">CodePier</option>
      </select>

      <base-input
        validate
        v-model="form.number_of_workers"
        label="Number of Workers"
        name="number_of_workers"
      ></base-input>

      <base-checkbox
        label="Auto Start"
        v-model="form.auto_start"
        name="auto_start"
      ></base-checkbox>
      <base-checkbox
        label="Auto Restart"
        v-model="form.auto_restart"
        name="auto_restart"
      ></base-checkbox>

      <template slot="buttons">
        <span class="btn" @click.prevent="cancel">Cancel</span>
        <button
          class="btn btn-primary"
          :disabled="!form.isValid()"
          type="submit"
        >
          Add Worker
        </button>
      </template>
    </base-form>
  </section>
</template>

<script>
import Worker from "./components/Worker";
export default {
  components: {
    Worker,
  },
  data() {
    return {
      showForm: false,
      form: this.createForm({
        user: null,
        command: null,
        working_directory: null,
        number_of_workers: null,
      }).validation({
        rules: {
          user: "required",
          command: "required|max:255",
          number_of_workers: "required|min:1",
          // working_directory: "", // TODO - must be a valid directory
        },
      }),
    };
  },
  watch: {
    $route: {
      immediate: true,
      handler: "fetchData",
    },
    workers: {
      handler() {
        if (!this.workers.length) {
          this.showForm = true;
        }
      },
    },
  },
  methods: {
    fetchData() {
      this.$store.dispatch("user/sites/workers/get", {
        site: this.$route.params.site,
      });
    },
    createWorker() {
      this.$store
        .dispatch("user/sites/workers/create", {
          data: this.form.data(),
          parameters: {
            site: this.$route.params.site,
          },
        })
        .then(() => {
          this.cancel();
        });
    },
    deleteWorker(workerId) {
      this.$store.dispatch("user/sites/workers/destroy", {
        worker: workerId,
        site: this.$route.params.site,
      });
    },
    cancel() {
      this.form.reset();
      this.showForm = false;
    },
  },
  computed: {
    workers() {
      return this.$store.state.user.sites.workers.workers;
    },
  },
};
</script>

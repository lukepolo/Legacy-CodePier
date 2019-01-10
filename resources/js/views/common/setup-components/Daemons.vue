<template>
  <section>
    <div class="flex flex--center">
      <h3 class="flex--grow">Daemons</h3>

      <tooltip message="Add Daemon">
        <span
          class="btn btn-small btn-primary"
          :class="{ 'btn-disabled': this.showForm === true }"
          @click="showForm = true"
        >
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
        <tr v-for="daemon in daemons">
          <daemon :daemon="daemon"></daemon>
        </tr>
      </tbody>
    </table>

    <base-form v-form="form" :action="createDaemon" v-if="showForm">
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

      <select v-model="form.user" name="user" label="User to run daemon">
        <option value="root">Root</option>
        <option value="codepier">CodePier</option>
      </select>

      <template slot="buttons">
        <span class="btn" @click.prevent="cancel">Cancel</span>
        <button
          class="btn btn-primary"
          :disabled="!form.isValid()"
          type="submit"
        >
          Add Daemon
        </button>
      </template>
    </base-form>
  </section>
</template>

<script>
import Daemon from "./components/Daemon";
export default {
  components: { Daemon },
  data() {
    return {
      showForm: false,
      form: this.createForm({
        user: null,
        command: null,
        working_directory: null,
      }).validation({
        rules: {
          user: "required",
          command: "required|max:255",
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
    daemons: {
      handler() {
        if (!this.daemons.length) {
          this.showForm = true;
        }
      },
    },
  },
  methods: {
    fetchData() {
      this.$store.dispatch("user/sites/daemons/get", {
        site: this.$route.params.site,
      });
    },
    createDaemon() {
      this.$store
        .dispatch("user/sites/daemons/create", {
          data: this.form.data(),
          parameters: {
            site: this.$route.params.site,
          },
        })
        .then(() => {
          this.cancel();
        });
    },
    deleteDaemon(daemonId) {
      this.$store.dispatch("user/sites/daemons/destroy", {
        daemon: daemonId,
        site: this.$route.params.site,
      });
    },
    cancel() {
      this.form.reset();
      this.showForm = false;
    },
  },
  computed: {
    daemons() {
      return this.$store.state.user.sites.daemons.daemons;
    },
  },
};
</script>

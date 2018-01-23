<template>
    <section>

        <div class="flex flex--center">
            <h3 class="flex--grow">
                Environment Variables
            </h3>

            <tooltip message="Add Environment Variable">
                <span class="btn btn-small btn-primary" :class="{ 'btn-disabled' : this.shouldShowForm }" @click="showForm = true">
                    <span class="icon-plus"></span>
                </span>
            </tooltip>
        </div>

        <table class="table" v-if="environmentVariables.length">
            <thead>
            <tr>
                <th>Variable</th>
                <th>Value</th>
                <th></th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="environmentVariable in environmentVariables">
                <td>{{ environmentVariable.variable }}</td>
                <td>{{ environmentVariable.value }}</td>
                <td>
                    <template v-if="isRunningCommandFor(environmentVariable.id)">
                        {{ isRunningCommandFor(environmentVariable.id).status }}
                    </template>
                </td>
                <td class="table--action">
                    <tooltip message="Delete">
                        <span class="table--action-delete">
                            <a @click="deleteEnvironmentVariable(environmentVariable.id)"><span class="icon-trash"></span></a>
                        </span>
                    </tooltip>
                </td>
            </tr>
            </tbody>
        </table>

        <form @submit.prevent="createEnvironmentVariable" v-if="shouldShowForm">
            <div class="flyform--group">
                <input type="text" name="variable" v-model="form.variable" placeholder=" ">
                <label for="variable">Variable</label>
            </div>

            <div class="flyform--group">
                <input type="text" name="value" v-model="form.value" placeholder=" ">
                <label for="value">Value</label>
            </div>

            <div class="flyform--footer">
                <div class="flyform--footer-btns">
                    <button class="btn" v-if="environmentVariables.length" @click.prevent="resetForm">Cancel</button>
                    <button class="btn btn-primary" type="submit">Create Environment Variable</button>
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
        value: "",
        variable: null
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
          .dispatch("user_site_environment_variables/get", this.siteId)
          .then(() => {
            this.loaded = true;
          });
      }

      if (this.serverId) {
        this.$store
          .dispatch("user_server_environment_variables/get", this.serverId)
          .then(() => {
            this.loaded = true;
          });
      }
    },
    createEnvironmentVariable() {
      if (this.siteId) {
        this.$store
          .dispatch("user_site_environment_variables/store", {
            site: this.siteId,
            value: this.form.value,
            variable: this.form.variable
          })
          .then(environmentVariable => {
            if (environmentVariable) {
              this.resetForm();
            }
          });
      }

      if (this.serverId) {
        this.$store
          .dispatch("user_server_environment_variables/store", {
            server: this.serverId,
            value: this.form.value,
            variable: this.form.variable
          })
          .then(environmentVariable => {
            if (environmentVariable) {
              this.resetForm();
            }
          });
      }
    },
    deleteEnvironmentVariable(environmentVariableId) {
      if (this.siteId) {
        this.$store.dispatch("user_site_environment_variables/destroy", {
          site: this.siteId,
          environment_variable: environmentVariableId
        });
      }

      if (this.serverId) {
        this.$store.dispatch("user_server_environment_variables/destroy", {
          server: this.serverId,
          environment_variable: environmentVariableId
        });
      }
    },
    isRunningCommandFor(id) {
      return this.isCommandRunning("App\\Models\\EnvironmentVariable", id);
    },
    resetForm() {
      this.showForm = false;
      this.form.value = null;
      this.form.variable = null;
    }
  },
  computed: {
    site() {
      return this.$store.state.user_sites.site;
    },
    siteId() {
      return this.$route.params.site_id;
    },
    serverId() {
      return this.$route.params.server_id;
    },
    shouldShowForm() {
      return (
        (this.loaded && this.environmentVariables.length === 0) || this.showForm
      );
    },
    environmentVariables() {
      if (this.siteId) {
        return this.$store.state.user_site_environment_variables
          .environment_variables;
      }

      if (this.serverId) {
        return this.$store.state.user_server_environment_variables
          .environment_variables;
      }
    }
  }
};
</script>

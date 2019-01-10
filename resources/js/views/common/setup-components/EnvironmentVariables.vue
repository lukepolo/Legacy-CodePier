<template>
  <section>
    <div class="flex flex--center">
      <h3 class="flex--grow">Environment Variables</h3>

      <tooltip message="Add SSH Key">
        <span
          class="btn btn-small btn-primary"
          :class="{ 'btn-disabled': this.showForm === true }"
          @click="showForm = true"
        >
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
            <!--<template v-if="isRunningCommandFor(environmentVariable.id)">-->
            <!--{{ isRunningCommandFor(environmentVariable.id).status }}-->
            <!--</template>-->
          </td>
          <td class="table--action">
            <tooltip message="Delete">
              <span class="table--action-delete">
                <a @click="deleteEnvironmentVariable(environmentVariable.id)"
                  ><span class="icon-trash"></span
                ></a>
              </span>
            </tooltip>
          </td>
        </tr>
      </tbody>
    </table>

    <base-form
      v-form="form"
      :action="createEnvironmentVariable"
      v-if="showForm"
    >
      <base-input
        validate
        v-model="form.variable"
        label="Variable"
        name="variable"
      ></base-input>
      <base-input
        validate
        v-model="form.value"
        label="Value"
        name="value"
      ></base-input>

      <template slot="buttons">
        <span class="btn" @click.prevent="cancel">Cancel</span>
        <button
          class="btn btn-primary"
          :disabled="!form.isValid()"
          type="submit"
        >
          Add Environment Variable
        </button>
      </template>
    </base-form>
  </section>
</template>

<script>
export default {
  data() {
    return {
      showForm: false,
      form: this.createForm({
        name: null,
        ssh_key: null,
      }).validation({
        rules: {
          variable: "required|alpha|max:255",
          value: "required:255",
        },
      }),
    };
  },
  watch: {
    $route: {
      immediate: true,
      handler: "fetchData",
    },
    environmentVariables: {
      handler() {
        if (!this.environmentVariables.length) {
          this.showForm = true;
        }
      },
    },
  },
  methods: {
    fetchData() {
      this.$store.dispatch("user/sites/environmentVariables/get", {
        site: this.$route.params.site,
      });
    },
    createEnvironmentVariable() {
      this.$store
        .dispatch("user/sites/environmentVariables/create", {
          data: this.form.data(),
          parameters: {
            site: this.$route.params.site,
          },
        })
        .then(() => {
          this.cancel();
        });
    },
    deleteEnvironmentVariable(environmentVariableId) {
      this.$store.dispatch("user/sites/environmentVariables/destroy", {
        site: this.$route.params.site,
        environment_variable: environmentVariableId,
      });
    },
    cancel() {
      this.form.reset();
      this.showForm = false;
    },
  },
  computed: {
    environmentVariables() {
      return this.$store.state.user.sites.environmentVariables
        .environment_variables;
    },
  },
};
</script>

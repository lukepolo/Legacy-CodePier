<template>
  <section>
    <div class="flex flex--center">
      <h3 class="flex--grow">SSH Keys</h3>

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

    <table class="table" v-if="sshKeys.length">
      <thead>
        <tr>
          <th colspan="3">Key Name</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="sshKey in sshKeys">
          <td>{{ sshKey.name }}</td>
          <td class="table--action">
            <span class="icon-trash" @click="deleteSshKey(sshKey.id)"></span>
          </td>
        </tr>
      </tbody>
    </table>

    <base-form v-form="form" :action="createKey" v-if="showForm">
      <base-input
        validate
        v-model="form.name"
        label="Key Name"
        name="name"
      ></base-input>
      <base-input
        validate
        type="textarea"
        v-model="form.ssh_key"
        label="Public Key"
        name="ssh_key"
      ></base-input>

      <template slot="buttons">
        <span class="btn" @click.prevent="cancel">Cancel</span>
        <button class="btn btn-primary" type="submit">Add SSH Key</button>
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
          name: "required|max:255",
          ssh_key: "required",
        },
      }),
    };
  },
  watch: {
    $route: {
      immediate: true,
      handler: "fetchData",
    },
    sshKeys: {
      handler() {
        if (!this.sshKeys.length) {
          this.showForm = true;
        }
      },
    },
  },
  methods: {
    fetchData() {
      this.$store.dispatch("user/sites/sshKeys/get", {
        site: this.$route.params.site,
      });
    },
    createKey() {
      this.$store
        .dispatch("user/sites/sshKeys/create", {
          data: this.form.data(),
          parameters: {
            site: this.$route.params.site,
          },
        })
        .then(() => {
          this.cancel();
        });
    },
    deleteSshKey(sshKeyId) {
      this.$store.dispatch("user/sites/sshKeys/destroy", {
        ssh_key: sshKeyId,
        site: this.$route.params.site,
      });
    },
    cancel() {
      this.form.reset();
      this.showForm = false;
    },
  },
  computed: {
    sshKeys() {
      return this.$store.state.user.sites.sshKeys.ssh_keys;
    },
  },
};
</script>

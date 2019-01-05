<template>
  <section>
    <ssh-guide></ssh-guide>

    <base-form v-form="form" :action="createSshkey">
      <base-input
        validate
        name="name"
        label="Name"
        v-model="form.name"
      ></base-input>

      <base-input
        validate
        tooltip="Usually located at ~/.ssh/id_rsa.pub"
        type="textarea"
        validate
        name="ssh_key"
        label="Public SSH Key"
        v-model="form.ssh_key"
      ></base-input>

      <div slot="buttons">
        <button
          class="btn btn-primary"
          type="submit"
          :disabled="!form.isValid()"
        >
          Install SSH Key
        </button>
      </div>
    </base-form>

    <div v-if="userSshKeys.length">
      <h3>SSH Keys</h3>

      <table class="">
        <thead>
          <tr>
            <th>Key Name</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="key in userSshKeys">
            <td>{{ key.name }} {{ key.id }}</td>
            <td class="table--action">
              <tooltip message="Delete">
                <span class="table--action-delete">
                  <a @click.prevent="deleteSshKey(key.id)"
                    ><span class="icon-trash"></span
                  ></a>
                </span>
              </tooltip>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </section>
</template>

<script>
export default {
  data() {
    return {
      form: this.createForm({
        name: null,
        ssh_key: null,
      }).validation({
        rules: {
          name: "required",
          ssh_key: "required",
        },
      }),
    };
  },
  created() {
    this.fetchData();
  },
  methods: {
    fetchData() {
      this.$store.dispatch("user/sshKeys/get");
    },
    createSshkey() {
      this.$store
        .dispatch("user/sshKeys/create", {
          data: this.form.data(),
        })
        .then(() => {
          this.form.reset();
        });
    },
    deleteSshKey: function(sshKeyId) {
      this.$store.dispatch("user/sshKeys/destroy", {
        ssh_key: sshKeyId,
      });
    },
  },
  computed: {
    userSshKeys() {
      return this.$store.state.user.sshKeys.ssh_keys;
    },
  },
};
</script>

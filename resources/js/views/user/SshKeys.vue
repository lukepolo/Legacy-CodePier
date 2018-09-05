<template>
    <section>

        <ssh-guide></ssh-guide>

        <form v-form="form"  @submit.prevent="createSshkey">

            <base-input validate name="name" label="Name" v-model="form.name"></base-input>

            <base-input type="textarea" validate name="ssh_key" label="Public SSH Key" v-model="form.ssh_key"></base-input>

            <!-- TODO - add tooltip for base-input-->
            <div class="flyform--group">
                <tooltip message="Usually located at ~/.ssh/id_rsa.pub" size="medium" placement="top-right">
                    <span class="fa fa-info-circle"></span>
                </tooltip>
                <label class="flyform--group-iconlabel">Public Key</label>
                <textarea name="ssh_key" v-model="form.ssh_key"></textarea>
            </div>

            <div class="flyform--footer">
                <div class="flyform--footer-btns">
                    <button class="btn btn-primary" type="submit" :disabled="!form.isValid()">Install SSH Key</button>
                </div>
            </div>
        </form>

        <div v-if="user_ssh_keys.length">
            <h3>SSH Keys</h3>

            <table class="">
                <thead>
                <tr>
                    <th>Key Name</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="key in user_ssh_keys">
                    <td>{{ key.name }} {{ key.id }}</td>
                    <td class="table--action">
                        <tooltip message="Delete">
                            <span class="table--action-delete">
                                <a @click.prevent="deleteSshKey(key.id)"><span class="icon-trash"></span></a>
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
          name: "required|min:5",
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
    user_ssh_keys() {
      return this.$store.state.user.sshKeys.ssh_keys;
    },
  },
};
</script>

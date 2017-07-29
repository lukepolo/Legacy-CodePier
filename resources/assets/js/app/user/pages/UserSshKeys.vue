<template>
    <section>

        <ssh-guide></ssh-guide>

        <form @submit.prevent="createSshkey">

            <div class="flyform--group">
                <input name="name" type="text" v-model="form.name" placeholder=" ">
                <label for="name">Key Name</label>
            </div>

            <div class="flyform--group">
                <tooltip message="Usually located at ~/.ssh/id_rsa.pub" size="medium" placement="top-right">
                    <span class="fa fa-info-circle"></span>
                </tooltip>
                <label class="flyform--group-iconlabel">Public Key</label>
                <textarea name="ssh_key" v-model="form.ssh_key"></textarea>
            </div>

            <div class="flyform--footer">
                <div class="flyform--footer-btns">
                    <button class="btn btn-primary" type="submit">Install SSH Key</button>
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
                    <td>{{ key.name }}</td>
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
                    ssh_key: null
                })
            }
        },
        created() {
            this.fetchData()
        },
        methods: {
            fetchData() {
                this.$store.dispatch('user_ssh_keys/get')
            },
            createSshkey() {
                this.$store.dispatch('user_ssh_keys/store', this.form).then(() => {
                    this.form.reset()
                })
            },
            deleteSshKey: function (sshKeyId) {
                this.$store.dispatch('user_ssh_keys/destroy', sshKeyId)
            }
        },
        computed: {
            user_ssh_keys() {
                return this.$store.state.user_ssh_keys.ssh_keys
            }
        },
    }
</script>
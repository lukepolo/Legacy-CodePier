<template>
    <section>
        <p>
            This allows you to connect to your servers with the user "codepier" and the supplied SSH Keys.
        </p>
        <div class="jcf-form-wrap">
            <form @submit.prevent="createSshkey" class="floating-labels">

                <div class="jcf-input-group">
                    <input name="name" type="text" v-model="form.name">
                    <label for="name">
                        <span class="float-label">Name</span>
                    </label>
                </div>

                <div class="jcf-input-group">
                    <div class="input-question">Public Key</div>
                    <textarea name="ssh_key" v-model="form.ssh_key"></textarea>
                </div>

                <div class="btn-footer">
                    <button class="btn btn-primary" type="submit">Install SSH Key</button>
                </div>
            </form>
        </div>

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
                <td><a @click.prevent="deleteSshKey(key.id)" class="fa fa-remove"></a></td>
            </tr>
            </tbody>
        </table>
    </section>
</template>

<script>
    export default {
        data() {
            return {
                form: {
                    name: null,
                    ssh_key: null
                }
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
                    this.form = this.$options.data().form
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
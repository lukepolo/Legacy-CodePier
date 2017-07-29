<template>
    <section>

        <ssh-guide></ssh-guide>

        <form @submit.prevent="createKey">
            <div class="flyform--group">
                <input type="text" name="name" v-model="form.name" placeholder=" ">
                <label for="name">Key Name</label>
            </div>

            <div class="flyform--group">
                <label>Public Key</label>
                <textarea name="ssh_key" v-model="form.ssh_key"></textarea>
            </div>

            <div class="flyform--footer">
                <div class="flyform--footer-btns">
                    <button class="btn btn-primary" type="submit">Install SSH KEY</button>
                </div>
            </div>
        </form>

        <br>

        <div v-if="sshKeys.length">
            <h3>Installed SHH Keys</h3>

            <table class="table">
                <thead>
                <tr>
                    <th colspan="3">Key Name</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="sshKey in sshKeys">
                    <td>{{ sshKey.name }}</td>
                    <template v-if="isRunningCommandFor(sshKey.id)">
                        <td>
                            {{ isRunningCommandFor(sshKey.id).status }}
                        </td>
                    </template>
                    <td class="table--action">
                        <tooltip message="Delete">
                            <span class="table--action-delete">
                                <a href="#" @click="deleteKey(sshKey.id)"><span class="icon-trash"></span></a>
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
            this.fetchData();
        },
        watch: {
            '$route': 'fetchData'
        },
        methods: {
            fetchData() {
                if(this.siteId) {
                    this.$store.dispatch('user_site_ssh_keys/get', this.siteId)
                }

                if(this.serverId) {
                    this.$store.dispatch('user_server_ssh_keys/get', this.serverId)
                }
            },
            createKey() {

                if(this.siteId) {
                    this.form.site = this.siteId
                    this.$store.dispatch('user_site_ssh_keys/store', this.form).then((sshKey) => {
                        if(sshKey.id) {
                            this.resetForm()
                        }
                    })
                }

                if(this.serverId) {
                    this.form.server = this.serverId
                    this.$store.dispatch('user_server_ssh_keys/store', this.form).then((sshKey) => {
                        if(sshKey.id) {
                            this.resetForm()
                        }
                    })
                }

            },
            deleteKey(sshKeyId) {
                if(this.siteId) {
                    this.$store.dispatch('user_site_ssh_keys/destroy', {
                        ssh_key: sshKeyId,
                        site: this.siteId
                    });
                }

                if(this.serverId) {
                    this.$store.dispatch('user_server_ssh_keys/destroy', {
                        ssh_key: sshKeyId,
                        server: this.serverId
                    });
                }
            },
            isRunningCommandFor(id) {
                return this.isCommandRunning('App\\Models\\SshKey', id)
            },
            resetForm() {
                this.form.reset()
            }
        },
        computed: {
            siteId() {
                return this.$route.params.site_id
            },
            serverId() {
                return this.$route.params.server_id
            },
            sshKeys() {
                if(this.siteId) {
                    return this.$store.state.user_site_ssh_keys.ssh_keys
                }

                if(this.serverId) {
                    return this.$store.state.user_server_ssh_keys.ssh_keys;
                }
            }
        }
    }
</script>
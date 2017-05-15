<template>
    <section>
        <div class="jcf-form-wrap">
            <form @submit.prevent="createKey" class="floating-labels">
                <div class="jcf-input-group">
                    <input type="text" name="name" v-model="form.name">
                    <label for="name">
                        <span class="float-label">Key Name</span>
                    </label>
                </div>

                <div class="jcf-input-group">
                    <div class="input-question">Public Key</div>
                    <textarea name="ssh_key" v-model="form.ssh_key"></textarea>
                </div>

                <div class="btn-footer">
                    <button class="btn btn-primary" type="submit">Install SSH KEY</button>
                </div>
            </form>
        </div>

        <table class="table">
            <thead>
            <tr>
                <th>Key Name</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="sshKey in sshKeys">
                <td>{{ sshKey.name }}</td>
                <td>
                    <template v-if="isRunningCommandFor(sshKey.id)">
                        {{ isRunningCommandFor(sshKey.id).status }}
                    </template>
                    <template v-else>
                        <a href="#" class="fa fa-remove" @click="deleteKey(sshKey.id)">remove</a>
                    </template>
                </td>
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
                    this.$store.dispatch('getServerSshKeys', this.serverId)
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
                    this.$store.dispatch('createServerSshKey', this.form).then((sshKey) => {
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
                    this.$store.dispatch('deleteServerSshKey', {
                        ssh_key: sshKeyId,
                        server: this.serverId
                    });
                }
            },
            isRunningCommandFor(id) {
                return this.isCommandRunning('App\\Models\\SshKey', id)
            },
            resetForm() {
                this.form = this.$options.data().form
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
                    return this.$store.state.serverSshKeysStore.server_ssh_keys;
                }
            }
        }
    }
</script>
<template>
    <div v-if="site">
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
    </div>
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
                this.$store.dispatch('getSiteSshKeys', this.$route.params.site_id)
            },
            createKey() {
                this.form.site = this.$route.params.site_id
                this.$store.dispatch('createSiteSshKey', this.form).then(() => {
                    this.form = this.$options.data().form
                })
            },
            deleteKey(sshKeyId) {
                this.$store.dispatch('deleteSiteSshKey', {
                    ssh_key: sshKeyId,
                    site: this.$route.params.site_id
                });
            },
            isRunningCommandFor(id) {
                return this.isCommandRunning('App\\Models\\SshKey', id)
            }
        },
        computed: {
            site() {
                return this.$store.state.sitesStore.site
            },
            sshKeys() {
                return this.$store.state.siteSshKeysStore.site_ssh_keys
            }
        }
    }
</script>
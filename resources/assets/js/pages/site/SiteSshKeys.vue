<template>
    <div v-if="site">
        <div class="jcf-form-wrap">
            <form @submit.prevent="createKey" class="floating-labels">
                <h3>Site SSH Keys</h3>

                <div class="jcf-input-group">
                    <input type="text" name="name" v-model="form.name">
                    <label for="name">
                        <span class="float-label">Key Name</span>
                    </label>
                </div>

                <div class="jcf-input-group">
                    <input type="text" name="ssh_key" v-model="form.ssh_key">
                    <label for="ssh_key">
                        <span class="float-label">Public Key - TODO , should be a textarea</span>
                    </label>
                </div>

                <div class="btn-footer">
                    <button class="btn btn-primary" type="submit">Install SSH KEY</button>
                </div>
            </form>
        </div>

        <table class="table" v-for="sshKey in sshKeys">
            <thead>
                <tr>
                    <th>Key Name</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ sshKey.name }}</td>
                    <td>
                        <template v-if="isRunningCommandFor(sshKey.id)">
                            {{ isRunningCommandFor(sshKey.id).status }}
                        </template>
                        <a href="#" class="fa fa-remove" @click="deleteKey(sshKey.id)">remove</a>
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
                    ssh_key: null,
                    site : this.$route ? this.$route.params.site_id : null,
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
                this.$store.dispatch('getSite', this.$route.params.site_id);
                this.$store.dispatch('getSiteSshKeys', this.$route.params.site_id);
            },
            createKey() {
                this.$store.dispatch('createSiteSshKey', this.form);
                this.form = this.$options.data().form;
            },
            deleteKey(sshKeyId) {
                this.$store.dispatch('deleteSiteSshKey', {
                    ssh_key: sshKeyId,
                    site: this.form.site,
                });
            },
            isRunningCommandFor(id) {
                return this.isCommandRunning('App\\Models\\SshKey', id);
            }
        },
        computed: {
            site() {
                return this.$store.state.sitesStore.site;
            },
            sshKeys() {
                return this.$store.state.siteSshKeysStore.site_ssh_keys;
            }
        }
    }
</script>
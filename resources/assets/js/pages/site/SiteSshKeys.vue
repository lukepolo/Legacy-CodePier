<template>
    <div v-if="site">
        Site SSH Keys
        <form @submit.prevent="createKey">
            <div class="form-group">
                <label>Name</label>
                <input type="text" name="name" v-model="form.name">
            </div>
            <div class="form-group">
                <label>Public Key</label>
                <textarea name="ssh_key" v-model="form.ssh_key"></textarea>
            </div>
            <button type="submit">Install SSH KEY</button>
        </form>

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
                    <td><a href="#" class="fa fa-remove" @click="deleteKey(sshKey.id)">remove</a></td>
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
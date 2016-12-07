<template>
    <section>
        <section id="middle" class="section-column" v-if="server">
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

            <table class="table" v-if="ssh_keys.length" v-for="ssh_key in ssh_keys">
                <thead>
                <tr>
                    <th>Key Name</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>{{ ssh_key.name }}</td>
                    <td><a href="#" class="fa fa-remove" @click="deleteKey(ssh_key.id)">remove</a></td>
                </tr>
                </tbody>
            </table>
        </section>
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
                this.$store.dispatch('getServer', this.$route.params.server_id);
                this.$store.dispatch('getServerSshKeys', this.$route.params.server_id);
            },
            createKey() {
                this.form['server'] = this.server.id;
                this.$store.dispatch('createServerSshKey', this.form);
            },
            deleteKey(ssh_key_id) {
                this.$store.dispatch('deleteServerSshKey', {
                    ssh_key: ssh_key_id,
                    server: this.server.id
                });
            }
        },
        computed: {
            server() {
                return this.$store.state.serversStore.server;
            },
            ssh_keys() {
                return this.$store.state.serverSshKeysStore.server_ssh_keys;
            }
        }
    }
</script>

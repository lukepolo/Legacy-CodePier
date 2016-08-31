<template>
    <section>
        <left-nav></left-nav>
        <section id="middle" class="section-column" v-if="server">
            <server-nav :server="server"></server-nav>
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
    import ServerNav from './components/ServerNav.vue';
    import LeftNav from './../../core/LeftNav.vue';

    export default {
        components : {
            LeftNav,
            ServerNav
        },
        data() {
            return {
                form : {
                    name : null,
                    ssh_key :null
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
            fetchData: function () {
                serverStore.dispatch('getServer', this.$route.params.server_id);
                serverSshKeyStore.dispatch('getServerSshKeys', this.$route.params.server_id);
            },
            createKey() {
                this.form['server'] = this.server.id;
                serverSshKeyStore.dispatch('createServerSshKey', this.form);
            },
            deleteKey(ssh_key_id) {
                serverSshKeyStore.dispatch('deleteServerSshKey', {
                    ssh_key : ssh_key_id,
                    server : this.server.id
                });
            }
        },
        computed : {
            server : () => {
                return serverStore.state.server;
            },
            ssh_keys : () => {
                return serverSshKeyStore.state.server_ssh_keys;
            }
        }
    }
</script>

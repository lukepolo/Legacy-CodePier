<template>
    <section>
        <left-nav></left-nav>
        <section id="middle" class="section-column" v-if="server">
            <server-nav :server="server"></server-nav>
            <form v-on:submit.prevent="onSubmit">
                <input type="hidden" name="server_id" :value="server.id">
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" name="name">
                </div>
                <div class="form-group">
                    <label>Public Key</label>
                    <textarea name="ssh_key"></textarea>
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
                    <td><a href="#" class="fa fa-remove" v-on:click="deleteKey(ssh_key.id)">remove</a></td>
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
                server : null,
                ssh_keys : []
            }
        },
        methods : {
            onSubmit() {
                Vue.http.post(this.action('Server\Features\ServerSshKeyController@store'), this.getFormData($(this.$el))).then((response) => {
                    this.ssh_keys.push(response.json());
                }, (errors) => {
                    alert(error);
                });
            },
            deleteKey(ssh_key_id) {
                Vue.http.delete(this.action('Server\Features\ServerSshKeyController@destroy', { ssh_key : ssh_key_id })).then((response) => {
                    this.getSshKeys();
                }, (errors) => {
                    alert(error);
                });
            },
            getSshKeys() {
                Vue.http.get(this.action('Server\Features\ServerSshKeyController@index', {server_id : this.$route.params.server_id})).then((response) => {
                    this.ssh_keys = response.json();
                }, (errors) => {
                    alert(error);
                });
            }

        },
        mounted() {
            Vue.http.get(this.action('Server\ServerController@show', {server : this.$route.params.server_id})).then((response) => {
                this.server = response.json();
            }, (errors) => {
                alert(error);
            });

            this.getSshKeys();

        }
    }
</script>

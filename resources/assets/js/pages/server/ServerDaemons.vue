<template>
    <section>
        <left-nav></left-nav>
        <section id="middle" class="section-column" v-if="server">
            <server-nav :server="server"></server-nav>
            <form v-on:submit.prevent="onSubmit">
                <input type="hidden" name="server_id" :value="server.id">
                Command
                <input type="text" name="command">
                User
                <select name="user">
                    <option value="root">Root User</option>
                    <option value="codepier">CodePier User</option>
                </select>
                <input type="checkbox" name="auto_start"> Auto Start
                <input type="checkbox" name="auto_restart"> Auto Restart
                Workers
                <input type="integer" name="number_of_workers">

                <button type="submit">Create Cron</button>
            </form>

            <table class="table" v-if="daemons" v-for="daemon in daemons">
                <thead>
                <tr>
                    <th>Command</th>
                    <th>User</th>
                    <th>Auto Start</th>
                    <th>Auto Restart</th>
                    <th>Number of Workers</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ daemon.command }}</td>
                        <td>{{ daemon.user }}</td>
                        <td>{{ daemon.auto_start }}</td>
                        <td>{{ daemon.auto_restart }}</td>
                        <td>{{ daemon.number_of_workers }}</td>
                        <td><a href="#" class="fa fa-remove" v-on:click="deleteDaemon(daemon.id)">remove</a></td>
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
                daemons : []
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
                Vue.http.get(this.action('Server\ServerController@show', {server : this.$route.params.server_id})).then((response) => {
                    this.server = response.json();
                }, (errors) => {
                    alert(error);
                });

                this.getDaemons();
            },
            onSubmit() {
                Vue.http.post(this.action('Server\Features\ServerDaemonController@store'), this.getFormData($(this.$el))).then((response) => {
                    this.daemons.push(response.json());
                }, (errors) => {
                    alert(error);
                });
            },
            deleteDaemon(daemon_id) {
                Vue.http.delete(this.action('Server\Features\ServerDaemonController@destroy', { daemon : daemon_id })).then((response) => {
                    this.getDaemons();
                }, (errors) => {
                    alert(error);
                });
            },
            getDaemons() {
                Vue.http.get(this.action('Server\Features\ServerDaemonController@index', {server_id : this.$route.params.server_id})).then((response) => {
                    this.daemons = response.json();
                }, (errors) => {
                    alert(error);
                });
            }

        }
    }
</script>

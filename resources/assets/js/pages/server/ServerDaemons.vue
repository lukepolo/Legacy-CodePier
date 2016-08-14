<template>
    <section>
        <left-nav></left-nav>
        <section id="middle" class="section-column">
            <server-nav :server_id="this.$route.params.server_id"></server-nav>
            <form>
                Command
                <input type="text" name="command">
                User
                <input type="text" name="user">
                <input type="checkbox" name="auto_start"> Auto Start
                <input type="checkbox" name="auto_restart"> Auto Restart
                Workers
                <input type="integer" name="number_of_workers">

                <button type="submit">Create Cron</button>
            </form>

            <table class="table">
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
                    <td>command</td>
                    <td>user</td>
                    <td>auto_start</td>
                    <td>auto_restart</td>
                    <td>number_of_workers</td>
                    <td><a href="#" class="fa fa-remove"></a></td>
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
                server : null
            }
        },
        computed : {

        },
        methods : {

        },
        mounted() {
            Vue.http.get(this.action('Server\ServerController@show', {server : this.$route.params.server_id})).then((response) => {
                this.server = response.json();
            }, (errors) => {
                alert(error);
            });
        }
    }
</script>

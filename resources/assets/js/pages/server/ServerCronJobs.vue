<template>
    <section>
        <left-nav></left-nav>
        <section id="middle" class="section-column">
            <server-nav :server_id="this.$route.params.server_id"></server-nav>
            <span id="cron-preview"></span>
            <form>
                <input type="text" name="cron">
                <input type="hidden" name="cron_timing">
                <div id="cron-maker"></div>
                <button type="submit">Create Cron</button>
            </form>

            <table class="table">
                <thead>
                <tr>
                    <th>Job</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>cron job</td>
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

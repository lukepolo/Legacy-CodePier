<template>
    <section>
        <left-nav></left-nav>
        <section id="middle" class="section-column">
            <template v-if="server">
                <div class="panel-heading">
                    Server {{ server.name }} <small>{{ server.ip }}</small>
                    -- (DISK SPACE?)
                </div>
                <server-nav :server_id="this.$route.params.server_id"></server-nav>

                <table class="table">
                    <thead>
                    <tr>
                        <th>Domain</th>
                        <th>Repository</th>
                        <th>ZeroTime Deployment</th>
                        <th>Workers</th>
                        <th>WildCard Domain</th>
                        <th>SSL</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td><a href="#">site</a></td>
                        <td>repository</td>
                        <td>zero time deployment</td>
                        <td>0</td>
                        <td>wildcard</td>
                        <td>has active ssl?</td>
                    </tr>
                    </tbody>
                </table>
            </template>
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

<template>
    <section>
        <left-nav></left-nav>
        <section id="middle" class="section-column">
            <server-nav :server_id="this.$route.params.server_id"></server-nav>
            <div class="row">
                Connect to :
                <form>
                    <div class="checkbox">
                        <label>
                            <input type="servers[]"> server name - ip
                        </label>
                    </div>
                    <button type="submit">Connect to servers</button>
                </form>
            </div>
            <div class="row">
                <form>
                    description
                    <input type="text" name="description">
                    from ip
                    <input type="text" name="from_ip">
                    port
                    <input type="text" name="port">
                    <button type="submit">Add Firewall Rule</button>
                </form>

                <table class="table">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>From IP</th>
                        <th>Port</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>description</td>
                        <td>port</td>
                        <td>from ip</td>
                        <td><a href="#" class="fa fa-remove"></a></td>
                    </tr>
                    </tbody>
                </table>
            </div>
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

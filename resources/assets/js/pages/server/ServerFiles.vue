<template>
    <section>
        <left-nav></left-nav>
        <section id="middle" class="section-column" v-if="server">
            <server-nav :server="server"></server-nav>
            <div class="row">
                <form>
                    <div :data-server_id="this.$route.params.server_id" data-path="/etc/php/7.0/fpm/php.ini" class="editor">Loading . . . </div>
                    <button type="submit">Update PHP Config</button>
                </form>
                <form>
                    <div :data-server_id="this.$route.params.server_id" data-path="/etc/php/7.0/cli/php.ini" class="editor">Loading . . . </div>
                    <button type="submit">Update PHP CLI Config</button>
                </form>
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
            }
        }
    }
</script>

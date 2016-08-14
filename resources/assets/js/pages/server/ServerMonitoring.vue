<template>
    <section>
        <left-nav></left-nav>
        <section id="middle" class="section-column">
            <server-nav :server_id="this.$route.params.server_id"></server-nav>
            <div class="btn">Integrate With Slack</div>
            <div class="btn">Integrate With HipChat</div>
            <form>
                server id
                <input type="text" name="server_id">
                server token
                <input type="text" name="server_token">
                <button type="submit">Install Blackfire</button>
            </form>
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

<template>
    <section>
        <left-nav></left-nav>
        <section id="middle" class="section-column" v-if="server">
            <server-nav :server="server"></server-nav>

            Still needs to be thought out
            <div class="btn">Integrate With Slack</div>
            <div class="btn">Integrate With HipChat</div>

            // Servers can have plugins, should add plugins via DB
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
            },
            onSubmit() {
                Vue.http.post(this.action('Server\ServerFirewallController@store'), this.getFormData($(this.$el))).then((response) => {
                    this.firewall_rules.push(response.json());
                }, (errors) => {
                    alert(error);
                });
            }
        }
    }
</script>

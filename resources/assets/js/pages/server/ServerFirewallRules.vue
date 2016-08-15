<template>
    <section>
        <left-nav></left-nav>
        <section id="middle" class="section-column" v-if="server">
            <server-nav :server="server"></server-nav>
            <div class="row">
                Connect to :
                <form @submit.prevent="onSubmit">
                    <div class="checkbox">
                        <label>
                            <input type="servers[]"> server name - ip
                        </label>
                    </div>
                    <button type="submit">Connect to servers</button>
                </form>
            </div>
            <div class="row">
                <form @submit.prevent="onSubmitFirewallRules">
                    <input type="hidden" name="server_id" :value="server.id">
                    description
                    <input type="text" name="description">
                    from ip
                    <input type="text" name="from_ip">
                    port
                    <input type="text" name="port">
                    <button type="submit">Add Firewall Rule</button>
                </form>

                <table class="table" v-if="firewall_rules" v-for="firewall_rule in firewall_rules">
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
                        <td>{{ firewall_rule.description }}</td>
                        <td>{{ firewall_rule.port }}</td>
                        <td>{{ firewall_rule.from_ip }}</td>
                        <td><a href="#" class="fa fa-remove" v-on:click="deleteFirewallRule(firewall_rule.id)">remove</a></td>
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
                server : null,
                firewall_rules : []
            }
        },
        methods : {
            onSubmitFirewallRules() {
                Vue.http.post(this.action('Server\Features\ServerFirewallController@store'), this.getFormData($(this.$el))).then((response) => {
                    this.firewall_rules.push(response.json());
                }, (errors) => {
                    alert(error);
                });
            },
            deleteFirewallRule(firewall_rule_id) {
                Vue.http.delete(this.action('Server\Features\ServerFirewallController@destroy', { firewall : firewall_rule_id })).then((response) => {
                    this.getFirewallRules();
                }, (errors) => {
                    alert(error);
                });
            },
            getFirewallRules() {
                Vue.http.get(this.action('Server\Features\ServerFirewallController@index', {server_id : this.$route.params.server_id})).then((response) => {
                    this.firewall_rules = response.json();
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

            this.getFirewallRules();

        }
    }
</script>
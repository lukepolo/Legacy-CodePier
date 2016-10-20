<template>
    <section>
        <left-nav></left-nav>
        <section id="middle" class="section-column" v-if="server">
            <server-nav :server="server"></server-nav>
            <div class="row" v-if="availableServers.length">
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
                <form @submit.prevent="createFirewallRule">
                    description
                    <input type="text" name="description" v-model="form.description">
                    from ip
                    <input type="text" name="from_ip" v-model="form.from_ip">
                    port
                    <input type="text" name="port" v-model="form.port">
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
                        <td><a class="fa fa-remove" @click.prevent="deleteFirewallRule(firewall_rule.id)">remove</a>
                        </td>
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
        components: {
            LeftNav,
            ServerNav
        },
        data() {
            return {
                form: {
                    port: null,
                    from_ip: null,
                    description: null,
                }
            }
        },
        created() {
            this.fetchData();
        },
        watch: {
            '$route': 'fetchData'
        },
        methods: {
            fetchData() {
                this.$store.dispatch('getServer', this.$route.params.server_id);
                this.$store.dispatch('getServerFirewallRules', this.$route.params.server_id)
            },
            createFirewallRule() {
                this.form['server'] = this.server.id;
                this.$store.dispatch('createServerFirewallRule', this.form)
            },
            deleteFirewallRule(firewall_rule_id) {
                this.$store.dispatch('deleteServerFirewallRule', {
                    server: this.server.id,
                    firewall: firewall_rule_id
                })
            }
        },
        computed: {
            server() {
                return this.$store.state.serversStoreserver;
            },
            firewall_rules() {
                return serverFirewallStore.state.server_firewall_rules;
            },
            availableServers() {
                return [];
            }
        }
    }
</script>
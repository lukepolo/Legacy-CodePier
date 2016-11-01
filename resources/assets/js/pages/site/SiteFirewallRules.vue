<template>
    <div v-if="site">
        Firewall Rules
        <form @submit.prevent="createFirewallRule">
            description
            <input type="text" name="description" v-model="form.description">
            from ip
            <input type="text" name="from_ip" v-model="form.from_ip">
            port
            <input type="text" name="port" v-model="form.port">
            <button type="submit">Add Firewall Rule</button>
        </form>

        <table class="table" v-for="firewallRule in firewallRules">
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
                <td>{{ firewallRule.description }}</td>
                <td>{{ firewallRule.port }}</td>
                <td>{{ firewallRule.from_ip }}</td>
                <td><a class="fa fa-remove" @click.prevent="deleteFirewallRule(firewallRule.id)">remove</a>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                form: {
                    port: null,
                    from_ip: null,
                    description: null,
                    site : this.$route.params.site_id,
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
                this.$store.dispatch('getSiteFirewallRules', this.$route.params.site_id);
                this.$store.dispatch('getSite', this.$route.params.site_id);
            },
            createFirewallRule() {
                this.$store.dispatch('createSiteFirewallRule', this.form)
            },
            deleteFirewallRule(firewallRuleId) {
                this.$store.dispatch('deleteSiteFirewallRule', {
                    site: this.form.site,
                    firewall_rule: firewallRuleId,
                })
            }
        },
        computed: {
            site() {
                return this.$store.state.sitesStore.site;
            },
            firewallRules() {
                return this.$store.state.siteFirewallRulesStore.site_firewall_rules;
            }
        }
    }
</script>
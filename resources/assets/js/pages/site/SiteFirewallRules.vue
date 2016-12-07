<template>
    <div v-if="site">

        <div class="jcf-form-wrap">
            <form @submit.prevent="createFirewallRule" class="floating-labels">
                <h3>Firewall Rules</h3>
                <div class="jcf-input-group">
                    <input type="text" name="description" v-model="form.description">
                    <label for="description">
                        <span class="float-label">Description</span>
                    </label>
                </div>

                <div class="jcf-input-group">
                    <input type="text" name="port" v-model="form.port">
                    <label for="port">
                        <span class="float-label">Port</span>
                    </label>
                </div>

                <div class="jcf-input-group">
                    <input type="text" name="from_ip" v-model="form.from_ip">
                    <label for="from_ip">
                        <span class="float-label">From IP</span>
                    </label>
                </div>

                <div class="btn-footer">
                    <button class="btn btn-primary" type="submit">Add Firewall Rule</button>
                </div>


            </form>
        </div>

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
                    site : this.$route ? this.$route.params.site_id : null,
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
                this.$store.dispatch('createSiteFirewallRule', this.form);
                this.form = this.$options.data().form;
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
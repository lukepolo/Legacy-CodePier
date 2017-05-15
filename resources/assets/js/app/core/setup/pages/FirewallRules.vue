<template>
    <section>
        <div class="jcf-form-wrap">
            <form @submit.prevent="createFirewallRule" class="floating-labels">
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

                <div class="jcf-input-group">
                    <div class="input-question">Select Type</div>
                    <div class="select-wrap">
                        <select v-model="form.type" name="type">
                            <option>tcp</option>
                            <option>udp</option>
                        </select>
                    </div>
                </div>

                <div class="btn-footer">
                    <button class="btn btn-primary" type="submit">Add Firewall Rule</button>
                </div>
            </form>
        </div>

        <table class="table">
            <thead>
            <tr>
                <th>Name</th>
                <th>Port</th>
                <th>From IP</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="firewallRule in firewallRules">
                <td>{{ firewallRule.description }}</td>
                <td>{{ firewallRule.port }}  {{ firewallRule.type }}</td>
                <td>{{ firewallRule.from_ip }}</td>
                <td>
                    <template v-if="isRunningCommandFor(firewallRule.id)">
                        {{ isRunningCommandFor(firewallRule.id).status }}
                    </template>
                    <template v-else>
                        <a class="fa fa-remove" @click.prevent="deleteFirewallRule(firewallRule.id)">remove</a>
                    </template>
                </td>
            </tr>
            </tbody>
        </table>

    </section>
</template>

<script>
    export default {
        data() {
            return {
                form: {
                    port: null,
                    type : 'tcp',
                    from_ip: null,
                    description: null
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

                if(this.siteId) {
                    this.$store.dispatch('user_site_firewall_rules/get', this.siteId)
                }

                if(this.serverId) {
                    this.$store.dispatch('getServerFirewallRules', this.serverId)
                }

            },
            createFirewallRule() {

                if(this.siteId) {
                    this.form.site = this.siteId
                    this.$store.dispatch('user_site_firewall_rules/store', this.form).then((firewallRule) => {
                        if (firewallRule.id) {
                            this.resetForm()
                        }
                    })
                }

                if(this.serverId) {
                    this.form.server = this.serverId
                    this.$store.dispatch('createServerFirewallRule', this.form).then((firewallRule) => {
                        if (firewallRule.id) {
                            this.resetForm()
                        }
                    })
                }
            },
            deleteFirewallRule(firewallRuleId) {

                if(this.siteId) {
                    this.$store.dispatch('user_site_firewall_rules/destroy', {
                        site: this.siteId,
                        firewall_rule: firewallRuleId
                    })
                }

                if(this.serverId) {
                    this.$store.dispatch('deleteServerFirewallRule', {
                        server: this.serverId,
                        firewall: firewallRuleId
                    })
                }
            },
            isRunningCommandFor(id) {
                return this.isCommandRunning('App\\Models\\FirewallRule', id)
            },
            resetForm() {
                this.form = this.$options.data().form
            }
        },
        computed: {
            siteId() {
                return this.$route.params.site_id
            },
            serverId() {
                return this.$route.params.server_id
            },
            firewallRules() {
                if(this.siteId) {
                    return this.$store.state.user_site_firewall_rules.firewall_rules
                }

                if(this.serverId) {
                    return this.$store.state.serverFirewallStore.server_firewall_rules;
                }
            }
        }
    }
</script>
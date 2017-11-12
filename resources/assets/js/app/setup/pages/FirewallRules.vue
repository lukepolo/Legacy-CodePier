<template>
    <section>
        <div class="flex flex--center">
            <h3 class="flex--grow">
                Firewall Rules
            </h3>

            <tooltip message="Add Firewall Rule">
                <span class="btn btn-small btn-primary" @click="showForm = true">
                    <span class="icon-plus"></span>
                </span>
            </tooltip>
        </div>
        <table class="table" v-if="firewallRules.length">
            <thead>
            <tr>
                <th>Name</th>
                <th>Port</th>
                <th>From IP</th>
                <th></th>
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
                    </td>

                    <td class="table--action">
                        <tooltip message="Delete">
                                <span class="table--action-delete">
                                    <a @click.prevent="deleteFirewallRule(firewallRule.id)"><span class="icon-trash"></span></a>
                                </span>
                        </tooltip>
                    </td>
                </tr>
            </tbody>
        </table>

        <form @submit.prevent="createFirewallRule" v-if="(loaded && firewallRules.length === 0) || showForm">
            <div class="flyform--group">
                <input type="text" name="description" v-model="form.description" placeholder=" ">
                <label for="description">Description</label>
            </div>

            <div class="grid-2">
                <div class="flyform--group">
                    <input type="text" name="port" v-model="form.port" placeholder=" ">
                    <label for="port">Port</label>
                </div>

                <div class="flyform--group">
                    <label>Select Type</label>
                    <div class="flyform--group-select">
                        <select v-model="form.type" name="type">
                            <option value="tcp">TCP</option>
                            <option value="udp">UDP</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="flyform--group">
                <input type="text" name="from_ip" v-model="form.from_ip" placeholder=" ">
                <label for="from_ip">From IP <small>(optional)</small></label>
            </div>

            <div class="flyform--footer">
                <div class="flyform--footer-btns">
                    <button class="btn" type="submit" v-if="firewallRules.length" @click.prevent="resetForm">Cancel</button>
                    <button class="btn btn-primary" type="submit">Add Firewall Rule</button>
                </div>
            </div>
        </form>
    </section>
</template>

<script>
    export default {
        data() {
            return {
                loaded : false,
                showForm : false,
                form: this.createForm({
                    port: null,
                    type : 'tcp',
                    from_ip: null,
                    description: null
                })
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
                    this.$store.dispatch('user_site_firewall_rules/get', this.siteId).then(() => {
                        this.loaded = true
                    })
                }

                if(this.serverId) {
                    this.$store.dispatch('user_server_firewall_rules/get', this.serverId).then(() => {
                        this.loaded = true
                    })
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
                    this.$store.dispatch('user_server_firewall_rules/store', this.form).then((firewallRule) => {
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
                    this.$store.dispatch('user_server_firewall_rules/destroy', {
                        server: this.serverId,
                        firewall_rule: firewallRuleId
                    })
                }
            },
            isRunningCommandFor(id) {
                return this.isCommandRunning('App\\Models\\FirewallRule', id)
            },
            resetForm() {
                this.form.reset();
                this.showForm = false;
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
                    return this.$store.state.user_server_firewall_rules.firewall_rules;
                }
            }
        }
    }
</script>
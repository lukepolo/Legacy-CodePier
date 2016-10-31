<template>
    <div v-if="site">
        Firewall Rules
        <template v-for="firewallRule in firewallRules">
            {{ firewallRule.id }}
        </template>
    </div>
</template>

<script>
    export default {
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
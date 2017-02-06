export default {
    state: {
        site_firewall_rules: []
    },
    actions: {
        getSiteFirewallRules: ({ commit }, site_id) => {
            Vue.http.get(Vue.action('Site\SiteFirewallRuleController@index', { site: site_id })).then((response) => {
                commit('SET_SITE_FIREWALL_RULES', response.data)
            }, (errors) => {
                app.handleApiError(errors)
            })
        },
        createSiteFirewallRule: ({ commit }, data) => {
            Vue.http.post(Vue.action('Site\SiteFirewallRuleController@store', { site: data.site }), data).then((response) => {
                // commit('ADD_SITE_FIREWALL_RULE', response.data)
            }, (errors) => {
                app.handleApiError(errors)
            })
        },
        deleteSiteFirewallRule: ({ commit }, data) => {
            Vue.http.delete(Vue.action('Site\SiteFirewallRuleController@destroy', {
                site: data.site,
                firewall_rule: data.firewall_rule
            })).then((response) => {
                commit('REMOVE_SITE_FIREWALL_RULE', data.firewall_rule)
            }, (errors) => {
                app.handleApiError(errors)
            })
        }
    },
    mutations: {
        ADD_SITE_FIREWALL_RULE: (state, firewall_rule) => {
            state.site_firewall_rules.push(firewall_rule)
        },
        REMOVE_SITE_FIREWALL_RULE: (state, firewall_rule_id) => {
            Vue.set(state, 'site_firewall_rules', _.reject(state.site_firewall_rules, { id: firewall_rule_id }))
        },
        SET_SITE_FIREWALL_RULES: (state, site_firewall_rules) => {
            state.site_firewall_rules = site_firewall_rules
        }
    }
}

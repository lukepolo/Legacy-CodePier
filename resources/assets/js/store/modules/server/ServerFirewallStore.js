export default {
    state: {
        server_firewall_rules: []
    },
    actions: {
        getServerFirewallRules: ({ commit }, server_id) => {
            Vue.http.get(Vue.action('Server\ServerFirewallRuleController@index', { server: server_id })).then((response) => {
                commit('SET_SERVER_FIREWALL_RULES', response.data)
            }, (errors) => {
                app.handleApiError(errors)
            })
        },
        createServerFirewallRule: ({ commit }, data) => {
            Vue.http.post(Vue.action('Server\ServerFirewallRuleController@store', { server: data.server }), data).then((response) => {
                commit('ADD_SERVER_FIREWALL_RULE', response.data)
            }, (errors) => {
                app.handleApiError(errors)
            })
        },
        deleteServerFirewallRule: ({ commit }, data) => {
            Vue.http.delete(Vue.action('Server\ServerFirewallRuleController@destroy', {
                server: data.server,
                firewall: data.firewall
            })).then(() => {
                commit('REMOVE_SERVER_FIREWALL_RULE', data.firewall)
            }, (errors) => {
                app.handleApiError(errors)
            })
        }
    },
    mutations: {
        ADD_SERVER_FIREWALL_RULE: (state, server_firewall_rule) => {
            state.server_firewall_rules.push(server_firewall_rule)
        },
        REMOVE_SERVER_FIREWALL_RULE: (state, server_firewall_rule_id) => {
            Vue.set(state, 'server_firewall_rules', _.reject(state.server_firewall_rules, { id: server_firewall_rule_id }))
        },
        SET_SERVER_FIREWALL_RULES: (state, server_firewall_rules) => {
            state.server_firewall_rules = server_firewall_rules
        }
    }
}

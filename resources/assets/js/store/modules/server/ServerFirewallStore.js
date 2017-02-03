export default {
    state: {
        server_firewall_rules: []
    },
    actions: {
        getServerFirewallRules: ({ commit }, serverId) => {
            Vue.http.get(Vue.action('Server\ServerFirewallRuleController@index', { server: serverId })).then((response) => {
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
        ADD_SERVER_FIREWALL_RULE: (state, serverFirewallRule) => {
            state.server_firewall_rules.push(serverFirewallRule)
        },
        REMOVE_SERVER_FIREWALL_RULE: (state, serverFirewallRuleId) => {
            Vue.set(state, 'server_firewall_rules', _.reject(state.server_firewall_rules, { id: serverFirewallRuleId }))
        },
        SET_SERVER_FIREWALL_RULES: (state, serverFirewallRules) => {
            state.server_firewall_rules = serverFirewallRules
        }
    }
}

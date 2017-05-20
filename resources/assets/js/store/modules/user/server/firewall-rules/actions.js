export const get = ({}, server) => {
    return Vue.request().get(
        Vue.action('Server\ServerFirewallRuleController@index', { server: server }),
        'user_server_firewall_rules/setAll'
    )
}

export const store = ({}, data) => {
    return Vue.request(data).post(
        Vue.action('Server\ServerFirewallRuleController@store', { server: data.server }),
        'user_server_firewall_rules/add'
    )
}

export const destroy = ({}, data) => {
    return Vue.request(data).delete(
        Vue.action('Server\ServerFirewallRuleController@destroy', {
            server: data.server,
            firewall_rule: data.firewall_rule
        }),
        'user_server_firewall_rules/remove'
    )
}

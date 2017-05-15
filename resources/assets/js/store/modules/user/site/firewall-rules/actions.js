export const get = ({}, site) => {
    return Vue.request().get(
        Vue.action('Site\SiteFirewallRuleController@index', { site: site }),
        'user_site_firewall_rules/setAll'
    )
}

export const store = ({}, data) => {
    return Vue.request(data).post(
        Vue.action('Site\SiteFirewallRuleController@store', { site: data.site }),
        'user_site_firewall_rules/add'
    )
}

export const destroy = ({}, data) => {
    return Vue.request(data).delete(
        Vue.action('Site\SiteFirewallRuleController@destroy', {
            site: data.site,
            firewall_rule: data.firewall_rule
        }),
        'user_site_firewall_rules/remove'
    )
}

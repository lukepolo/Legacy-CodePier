export const get = (context, site) => {
    return Vue.request().get(
        Vue.action("SiteSiteFirewallRuleController@index", { site: site }),
        "user_site_firewall_rules/setAll"
    );
};

export const store = (context, data) => {
    return Vue.request(data).post(
        Vue.action("SiteSiteFirewallRuleController@store", { site: data.site }),
        "user_site_firewall_rules/add"
    );
};

export const destroy = (context, data) => {
    return Vue.request(data).delete(
        Vue.action("SiteSiteFirewallRuleController@destroy", {
            site: data.site,
            firewall_rule: data.firewall_rule
        }),
        "user_site_firewall_rules/remove"
    );
};

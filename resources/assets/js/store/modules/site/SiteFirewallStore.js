export default {
    state: {
        site_firewall_rules: [],
    },
    actions: {
        getSiteFirewallRules: ({commit}, site_id) => {
            Vue.http.get(Vue.action('Site\SiteFirewallRuleController@index', {site: site_id})).then((response) => {
                commit('SET_SITE_FIREWALL_RULES', response.data);
            }, (errors) => {
                app.showError(errors);
            });
        },
        createSiteFirewallRule: ({commit, dispatch}, data) => {
            Vue.http.post(Vue.action('Site\SiteFirewallRuleController@store', {site: data.site}), data).then((response) => {
                dispatch('getSiteFirewallRules', data.site);
            }, (errors) => {
                app.showError(errors);
            });
        },
        deleteSiteFirewallRule: ({commit, dispatch}, data) => {
            Vue.http.delete(Vue.action('Site\SiteFirewallRuleController@destroy', {
                site: data.site,
                firewall_rule: data.firewall_rule
            })).then((response) => {
                dispatch('getSiteFirewallRules', data.site);
            }, (errors) => {
                app.showError(errors);
            });
        }
    },
    mutations: {
        SET_SITE_FIREWALL_RULES: (state, site_firewall_rules) => {
            state.site_firewall_rules = site_firewall_rules;
        }
    }
}
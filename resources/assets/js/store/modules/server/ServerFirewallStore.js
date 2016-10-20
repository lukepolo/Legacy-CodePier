export default {
    state: {
        server_firewall_rules: [],
    },
    actions: {
        getServerFirewallRules: ({commit}, server_id) => {
            Vue.http.get(Vue.action('Server\ServerFirewallController@index', {server: server_id})).then((response) => {
                commit('SET_SERVER_FIREWALL_RULES', response.data);
            }, (errors) => {
                alert(error);
            });
        },
        createServerFirewallRule: ({commit, dispatch}, data) => {
            Vue.http.post(Vue.action('Server\ServerFirewallController@store', {server: data.server}), data).then((response) => {
                dispatch('getServerFirewallRules', data.server);
            }, (errors) => {
                alert(error);
            });
        },
        deleteServerFirewallRule: ({commit, dispatch}, data) => {
            Vue.http.delete(Vue.action('Server\ServerFirewallController@destroy', {
                server: data.server,
                firewall: data.firewall
            })).then((response) => {
                dispatch('getServerFirewallRules', data.server);
            }, (errors) => {
                alert(error);
            });
        }
    },
    mutations: {
        SET_SERVER_FIREWALL_RULES: (state, server_firewall_rules) => {
            state.server_firewall_rules = server_firewall_rules;
        }
    }
}
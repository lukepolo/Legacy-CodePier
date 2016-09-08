import Vue from "vue/dist/vue";
import Vuex from "vuex";
import {action} from "./../helpers";

Vue.use(Vuex);

const serverFirewallStore = new Vuex.Store({
    state: {
        server_firewall_rules: [],
    },
    actions: {
        getServerFirewallRules: ({commit}, server_id) => {
            Vue.http.get(action('Server\ServerFirewallController@index', {server: server_id})).then((response) => {
                commit('SET_SERVER_FIREWALL_RULES', response.json());
            }, (errors) => {
                alert(error);
            });
        },
        createServerFirewallRule: ({commit}, data) => {
            Vue.http.post(action('Server\ServerFirewallController@store', {server: data.server}), data).then((response) => {
                serverFirewallStore.dispatch('getServerFirewallRules', data.server);
            }, (errors) => {
                alert(error);
            });
        },
        deleteServerFirewallRule: ({commit}, data) => {
            Vue.http.delete(action('Server\ServerFirewallController@destroy', {
                server: data.server,
                firewall: data.firewall
            })).then((response) => {
                serverFirewallStore.dispatch('getServerFirewallRules', data.server);
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
});

export default serverFirewallStore
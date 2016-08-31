import Vue from "vue/dist/vue";
import Vuex from "vuex";
import {action} from "./../helpers";

Vue.use(Vuex);

const serverStore = new Vuex.Store({
    state: {
        servers: [],
        server: null,
        server_sites: null
    },
    actions: {
        getServer : ({commit}, server_id) => {
            Vue.http.get(action('Server\ServerController@show', {server : server_id})).then((response) => {
                commit('SET_SERVER', response.json());
            }, (errors) => {
                alert(error);
            });
        },
        getServers: ({commit}, callback) => {
            Vue.http.get(action('Server\ServerController@index', {pile_id: pileStore.state.current_pile_id})).then((response) => {
                commit('SET_SERVERS', response.json());
                typeof callback === 'function' && callback();
            }, (errors) => {
                alert('handle some error')
            });
        },
        createServer: ({commit}, data) => {
            Vue.http.post(action('Server\ServerController@store'), data).then((response) => {
                alert('open events tab');
            }, (errors) => {
                alert(error);
            });
        },
        getServerSites: ({commit}, server_id) => {
            Vue.http.get(action('Server\ServerSiteController@index', {server : server_id})).then((response) => {
                commit('SET_SERVER_SITES', response.json());
            }, (errors) => {
                alert(error);
            });
        }
    },
    mutations: {
        SET_SERVER: (state, server) => {
            state.server = server;
        },
        SET_SERVERS: (state, servers) => {
            state.servers = servers;
        },
        SET_SERVER_SITES: (state, server_sites) => {
            state.server_sites = server_sites;
        }
    }
});

export default serverStore
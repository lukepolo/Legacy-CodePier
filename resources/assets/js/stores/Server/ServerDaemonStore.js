import Vue from "vue/dist/vue";
import Vuex from "vuex";
import {action} from "./../helpers";

Vue.use(Vuex);

const serverDaemonStore = new Vuex.Store({
    state: {
        server_daemons: [],
    },
    actions: {
        getServerDaemons: ({commit}, server_id) => {
            Vue.http.get(action('Server\ServerDaemonController@index', {server: server_id})).then((response) => {
                commit('SET_SERVER_DAEMONS', response.json());
            }, (errors) => {
                alert(error);
            });
        },
        createServerDaemon: ({commit}, data) => {
            Vue.http.post(action('Server\ServerDaemonController@store', {server: data.server}), data).then((response) => {
                serverDaemonStore.dispatch('getServerDaemons', data.server);
            }, (errors) => {
                alert(error);
            });
        },
        deleteServerDaemon: ({commit}, data) => {
            Vue.http.delete(action('Server\ServerDaemonController@destroy', {
                server: data.server,
                daemon: data.daemon
            })).then((response) => {
                serverDaemonStore.dispatch('getServerDaemons', data.server);
            }, (errors) => {
                alert(error);
            });
        }
    },
    mutations: {
        SET_SERVER_DAEMONS: (state, server_daemons) => {
            state.server_daemons = server_daemons;
        }
    }
});

export default serverDaemonStore
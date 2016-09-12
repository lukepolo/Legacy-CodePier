import Vue from "vue/dist/vue";
import Vuex from "vuex";
import {action} from "./../helpers";

Vue.use(Vuex);

const serverWorkerStore = new Vuex.Store({
    state: {
        server_workers: [],
    },
    actions: {
        getServerWorkers: ({commit}, server_id) => {
            Vue.http.get(action('Server\ServerWorkerController@index', {server: server_id})).then((response) => {
                commit('SET_SERVER_WORKERS', response.json());
            }, (errors) => {
                alert(error);
            });
        },
        createServerWorker: ({commit}, data) => {
            Vue.http.post(action('Server\ServerWorkerController@store', {server: data.server}), data).then((response) => {
                serverWorkerStore.dispatch('getServerWorkers', data.server);
            }, (errors) => {
                alert(error);
            });
        },
        deleteServerWorker: ({commit}, data) => {
            Vue.http.delete(action('Server\ServerWorkerController@destroy', {
                server: data.server,
                worker: data.worker
            })).then((response) => {
                serverWorkerStore.dispatch('getServerWorkers', data.server);
            }, (errors) => {
                alert(error);
            });
        }
    },
    mutations: {
        SET_SERVER_WORKERS: (state, server_workers) => {
            state.server_workers = server_workers;
        }
    }
});

export default serverWorkerStore
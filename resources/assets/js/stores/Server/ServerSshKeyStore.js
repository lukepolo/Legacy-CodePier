import Vue from "vue/dist/vue";
import Vuex from "vuex";
import {action} from "./../helpers";

const serverSshKeyStore = new Vuex.Store({
    state: {
        server_ssh_keys: [],
    },
    actions: {
        getServerSshKeys: ({commit}, server_id) => {
            Vue.http.get(action('Server\ServerSshKeyController@index', {server : server_id})).then((response) => {
                commit('SET_SERVER_SSH_KEYS', response.data);
            }, (errors) => {
                alert(error);
            });
        },
        createServerSshKey: ({commit}, data) => {
            Vue.http.post(action('Server\ServerSshKeyController@store', {server : data.server}), data).then((response) => {
                serverSshKeyStore.dispatch('getServerSshKeys', data.server);
            }, (errors) => {
                alert(error);
            });
        },
        deleteServerSshKey: ({commit}, data) => {
            Vue.http.delete(action('Server\ServerSshKeyController@destroy', { server : data.server, ssh_key : data.ssh_key })).then((response) => {
                serverSshKeyStore.dispatch('getServerSshKeys', data.server);
            }, (errors) => {
                alert(error);
            });
        }
    },
    mutations: {
        SET_SERVER_SSH_KEYS: (state, server_ssh_keys) => {
            state.server_ssh_keys = server_ssh_keys;
        }
    }
});

export default serverSshKeyStore
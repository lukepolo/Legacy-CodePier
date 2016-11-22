export default {
    state: {
        server_ssh_keys: [],
    },
    actions: {
        getServerSshKeys: ({commit}, server_id) => {
            Vue.http.get(Vue.action('Server\ServerSshKeyController@index', {server: server_id})).then((response) => {
                commit('SET_SERVER_SSH_KEYS', response.data);
            }, (errors) => {
                app.showError(errors);
            });
        },
        createServerSshKey: ({commit, dispatch}, data) => {
            Vue.http.post(Vue.action('Server\ServerSshKeyController@store', {server: data.server}), data).then((response) => {
                dispatch('getServerSshKeys', data.server);
            }, (errors) => {
                app.showError(errors);
            });
        },
        deleteServerSshKey: ({commit, dispatch}, data) => {
            Vue.http.delete(Vue.action('Server\ServerSshKeyController@destroy', {
                server: data.server,
                ssh_key: data.ssh_key
            })).then((response) => {
                dispatch('getServerSshKeys', data.server);
            }, (errors) => {
                app.showError(errors);
            });
        }
    },
    mutations: {
        SET_SERVER_SSH_KEYS: (state, server_ssh_keys) => {
            state.server_ssh_keys = server_ssh_keys;
        }
    }
}
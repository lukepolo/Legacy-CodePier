export default {
    state: {
        server_ssh_keys: [],
    },
    actions: {
        getServerSshKeys: ({commit}, server_id) => {
            Vue.http.get(Vue.action('Server\ServerSshKeyController@index', {server: server_id})).then((response) => {
                commit('SET_SERVER_SSH_KEYS', response.data);
            }, (errors) => {
                app.handleApiError(errors);
            });
        },
        createServerSshKey: ({commit}, data) => {
            Vue.http.post(Vue.action('Server\ServerSshKeyController@store', {server: data.server}), data).then((response) => {
                commit('ADD_SERVER_SSH_KEY', response.data);
            }, (errors) => {
                app.handleApiError(errors);
            });
        },
        deleteServerSshKey: ({commit}, data) => {
            Vue.http.delete(Vue.action('Server\ServerSshKeyController@destroy', {
                server: data.server,
                ssh_key: data.ssh_key
            })).then(() => {
                commit('REMOVE_SERVER_SSH_KEY',  data.ssh_key);
            }, (errors) => {
                app.handleApiError(errors);
            });
        }
    },
    mutations: {
        ADD_SERVER_SSH_KEY: (state, ssh_key) => {
            state.server_ssh_keys.push(ssh_key);
        },
        REMOVE_SERVER_SSH_KEY : (state, ssh_key_id) => {
            Vue.set(state, 'server_ssh_keys', _.reject(state.server_ssh_keys, { id : ssh_key_id }));
        },
        SET_SERVER_SSH_KEYS: (state, server_ssh_keys) => {
            state.server_ssh_keys = server_ssh_keys;
        }
    }
}
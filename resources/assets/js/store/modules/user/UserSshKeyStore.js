export default {
    state: {
        user_ssh_keys : []
    },
    actions: {
        getUserSshKeys: ({commit}) => {
            Vue.http.get(action('User\UserSshKeyController@index')).then((response) => {
                commit('SET_USER_SSH_KEYS', response.data);
            }, (errors) => {
                alert(error);
            });
        },
        createUserSshKey : ({commit}, data) => {
            Vue.http.post(action('User\UserSshKeyController@store'), {
                name : data.name,
                ssh_key : data.ssh_key
            }).then((response) => {
                userSshKeyStore.dispatch('getUserSshKeys');
            }, (errors) => {
                alert(error);
            });
        },
        deleteUserSshKey: ({commit}, ssh_key_id) => {
            Vue.http.delete(action('User\UserSshKeyController@destroy', { ssh_key : ssh_key_id })).then((response) => {
                userSshKeyStore.dispatch('getUserSshKeys');
            }, (errors) => {
                alert(error);
            });
        },
    },
    mutations: {
        SET_USER_SSH_KEYS: (state, keys) => {
            state.user_ssh_keys = keys;
        }
    }
}
export default {
    state: {
        user_ssh_keys : []
    },
    actions: {
        getUserSshKeys: ({commit}) => {
            Vue.http.get(Vue.action('User\UserSshKeyController@index')).then((response) => {
                commit('SET_USER_SSH_KEYS', response.data);
            }, (errors) => {
                alert(error);
            });
        },
        createUserSshKey : ({commit, dispatch}, data) => {
            Vue.http.post(Vue.action('User\UserSshKeyController@store'), {
                name : data.name,
                ssh_key : data.ssh_key
            }).then((response) => {
                dispatch('getUserSshKeys');
            }, (errors) => {
                alert(error);
            });
        },
        deleteUserSshKey: ({commit, dispatch}, ssh_key_id) => {
            Vue.http.delete(Vue.action('User\UserSshKeyController@destroy', { ssh_key : ssh_key_id })).then((response) => {
                dispatch('getUserSshKeys');
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
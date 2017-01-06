export default {
  state: {
    user_ssh_keys: []
  },
  actions: {
    getUserSshKeys: ({ commit }) => {
      Vue.http.get(Vue.action('User\UserSshKeyController@index')).then((response) => {
        commit('SET_USER_SSH_KEYS', response.data)
      }, (errors) => {
        app.handleApiError(errors)
      })
    },
    createUserSshKey: ({ commit }, data) => {
      Vue.http.post(Vue.action('User\UserSshKeyController@store'), {
        name: data.name,
        ssh_key: data.ssh_key
      }).then((response) => {
        commit('ADD_USER_SSH_KEY', response.data)
      }, (errors) => {
        app.handleApiError(errors)
      })
    },
    deleteUserSshKey: ({ commit }, ssh_key_id) => {
      Vue.http.delete(Vue.action('User\UserSshKeyController@destroy', { ssh_key: ssh_key_id })).then((response) => {
        commit('REMOVE_USER_SSH_KEY', ssh_key_id)
      }, (errors) => {
        app.handleApiError(errors)
      })
    }
  },
  mutations: {
    ADD_USER_SSH_KEY: (state, ssh_key) => {
      state.user_ssh_keys.push(ssh_key)
    },
    REMOVE_USER_SSH_KEY: (state, ssh_key_id) => {
      Vue.set(state, 'user_ssh_keys', _.reject(state.user_ssh_keys, { id: ssh_key_id }))
    },
    SET_USER_SSH_KEYS: (state, keys) => {
      state.user_ssh_keys = keys
    }
  }
}

export default {
  state: {
    notifications: []
  },
  actions: {
    addNotification: ({ commit }, notification) => {
      commit('ADD_NOTIFICATION', notification)
    },
    removeNotification: ({ commit }, notification) => {
      commit('REMOVE_NOTIFICATION', notification)
    }
  },
  mutations: {
    ADD_NOTIFICATION: (state, notification) => {
      state.notifications.push(notification)
    },
    REMOVE_NOTIFICATION: (state, notification) => {
      state.notifications = _.without(state.notifications, notification)
    }

  }
}

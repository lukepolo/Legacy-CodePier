export default {
  state: {
    notification_settings: [],
    notification_providers: [],
    user_notification_settings: [],
    user_notification_providers: []
  },
  actions: {
    getNotificationSettings: ({ commit }) => {
      Vue.http.get(Vue.action('NotificationSettingsController@index')).then((response) => {
        commit('SET_NOTIFICATION_SETTINGS', response.data)
      }, (errors) => {
        app.handleApiError(errors)
      })
    },
    getNotificationProviders: ({ commit }) => {
      Vue.http.get(Vue.action('Auth\Providers\NotificationProvidersController@index')).then((response) => {
        commit('SET_NOTIFICATION_PROVIDERS', response.data)
      }, (errors) => {
        app.handleApiError(errors)
      })
    },
    getUserNotificationProviders: ({ commit }, user_id) => {
      Vue.http.get(Vue.action('User\Providers\UserNotificationProviderController@index', { user: user_id })).then((response) => {
        commit('SET_USER_NOTIFICATION_PROVIDERS', response.data)
      }, (errors) => {
        app.handleApiError(errors)
      })
    },
    deleteUserNotificationProvider: ({ commit }, data) => {
      Vue.http.delete(Vue.action('User\Providers\UserNotificationProviderController@destroy', {
        user: data.user_id,
        notification_provider: data.user_notification_provider_id
      })).then((response) => {
        commit('REMOVE_NOTIFICATION_PROVIDER_FROM_USER', data.user_notification_provider_id)
      }, (errors) => {
        app.handleApiError(errors)
      })
    },
    getUserNotificationSettings: ({ commit }, user_id) => {
      Vue.http.get(Vue.action('User\UserNotificationSettingsController@index')).then((response) => {
        commit('SET_USER_NOTIFICATION_SETTINGS', response.data)
      }, (errors) => {
        app.handleApiError(errors)
      })
    },
    updateUserNotificationSettings: ({}, data) => {
      Vue.http.post(Vue.action('User\UserNotificationSettingsController@store'), data).then((response) => {

      }, (errors) => {
        app.handleApiError(errors)
      })
    }
  },
  mutations: {
    SET_NOTIFICATION_SETTINGS: (state, settings) => {
      state.notification_settings = settings
    },
    SET_NOTIFICATION_PROVIDERS: (state, providers) => {
      state.notification_providers = providers
    },
    SET_USER_NOTIFICATION_SETTINGS: (state, settings) => {
      state.user_notification_settings = settings
    },
    SET_USER_NOTIFICATION_PROVIDERS: (state, providers) => {
      state.user_notification_providers = providers
    },
    REMOVE_NOTIFICATION_PROVIDER_FROM_USER: (state, provider_id) => {
      Vue.set(state, 'user_notification_providers', _.reject(state.user_notification_providers, { id: provider_id }))
    }
  }
}

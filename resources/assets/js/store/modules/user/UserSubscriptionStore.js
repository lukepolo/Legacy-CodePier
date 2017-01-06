export default {
  state: {
    plans: [],
    user_invoices: [],
    user_subscription: null,
    valid_subscription: false,
    user_upcoming_subscription: null
  },
  actions: {
    getUserSubscription: ({ commit }) => {
      Vue.http.get(Vue.action('User\Subscription\UserSubscriptionController@index')).then((response) => {
        commit('SET_USER_SUBSCRIPTION', response.data)
      }, (errors) => {
        app.handleApiError(errors)
      })
    },
    getUpcomingSubscription: ({ commit }) => {
      Vue.http.get(Vue.action('User\Subscription\UserSubscriptionUpcomingInvoiceController@index')).then((response) => {
        commit('SET_USER_UPCOMING_SUBSCRIPTION', response.data)
      }, (errors) => {
        app.handleApiError(errors)
      })
    },
    createUserSubscription: ({ commit, dispatch }, data) => {
      return Vue.http.post(Vue.action('User\Subscription\UserSubscriptionController@store'), data).then((response) => {
        dispatch('getUserSubscription')
      }, (errors) => {
        app.handleApiError(errors)
      })
    },
    cancelSubscription: ({ commit, dispatch }, subscription) => {
      Vue.http.delete(Vue.action('User\Subscription\UserSubscriptionController@destroy', { subscription: subscription })).then((response) => {
        dispatch('getUserSubscription')
      }, (errors) => {
        app.handleApiError(errors)
      })
    },
    getUserInvoices: ({ commit }) => {
      Vue.http.get(Vue.action('User\Subscription\UserSubscriptionInvoiceController@index')).then((response) => {
        commit('SET_USER_INVOICES', response.data)
      }, (errors) => {
        app.handleApiError(errors)
      })
    },
    getPlans: ({ commit }) => {
      Vue.http.get(Vue.action('SubscriptionController@index')).then((response) => {
        commit('SET_PLANS', response.data)
      }, (errors) => {
        app.handleApiError(errors)
      })
    }
  },
  mutations: {
    SET_USER_SUBSCRIPTION: (state, subscription) => {
      state.valid_subscription = !_.isEmpty(subscription)
      state.user_subscription = subscription
    },
    SET_USER_UPCOMING_SUBSCRIPTION: (state, upcomingSubscription) => {
      state.user_upcoming_subscription = upcomingSubscription
    },
    SET_USER_INVOICES: (state, invoices) => {
      state.user_invoices = invoices
    },
    SET_PLANS: (state, plans) => {
      state.plans = plans
    }
  }
}

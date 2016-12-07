export default {
    state: {
        user_invoices: [],
        user_subscription: null,
        valid_subscription: false,
        user_upcoming_subscription: null
    },
    actions: {
        getUserSubscription: ({commit}) => {
            Vue.http.get(Vue.action('User\Subscription\UserSubscriptionController@index')).then((response) => {
                commit('SET_USER_SUBSCRIPTION', response.data);
            }, (errors) => {
                app.showError(errors);
            })
        },
        getUpcomingSubscription: ({commit}) => {
            Vue.http.get(Vue.action('User\Subscription\UserSubscriptionUpcomingInvoiceController@index')).then((response) => {
                commit('SET_USER_UPCOMING_SUBSCRIPTION', response.data);
            }, (errors) => {
                app.showError(errors);
            });
        },
        createUserSubscription: ({commit, dispatch}, data) => {
            Vue.http.post(Vue.action('User\Subscription\UserSubscriptionController@store'), data).then((response) => {
                dispatch('getUserSubscription');
            }, (errors) => {
                app.showError(errors);
            });
        },
        cancelSubscription: ({commit, dispatch}, subscription_id) => {
            Vue.http.delete(Vue.action('User\Subscription\UserSubscriptionController@destroy', {subscription: subscription_id})).then((response) => {
                dispatch('getUserSubscription');
            }, (errors) => {
                app.showError(errors);
            });
        },
        getUserInvoices: ({commit}) => {
            Vue.http.get(Vue.action('User\Subscription\UserSubscriptionInvoiceController@index')).then((response) => {
                commit('SET_USER_INVOICES', response.data);
            }, (errors) => {
                app.showError(errors);
            })
        }
    },
    mutations: {
        SET_USER_SUBSCRIPTION: (state, subscription) => {
            if (!_.isEmpty(subscription)) {
                state.valid_subscription = true;

                alert('see how we can do dispatch here');

                userSubscriptionStore.dispatch('getUpcomingSubscription');
            } else {
                state.valid_subscription = false;
            }

            state.user_subscription = subscription;
        },
        SET_USER_UPCOMING_SUBSCRIPTION: (state, upcoming_subscription) => {
            state.user_upcoming_subscription = upcoming_subscription;
        },
        SET_USER_INVOICES: (state, invoices) => {
            state.user_invoices = invoices;
        }
    }
}
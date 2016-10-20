export default {
    state: {
        user_invoices : [],
        user_subscription : null,
        valid_subscription : false,
        user_upcoming_subscription : null
    },
    actions: {
        getUserSubscription: ({commit}) => {
            Vue.http.get(action('User\Subscription\UserSubscriptionController@index')).then((response) => {
                commit('SET_USER_SUBSCRIPTION', response.data);
            }, (errors) => {
                alert(error);
            })
        },
        getUpcomingSubscription : ({commit}) => {
            Vue.http.get(action('User\Subscription\UserSubscriptionUpcomingInvoiceController@index')).then((response) => {
                commit('SET_USER_UPCOMING_SUBSCRIPTION', response.data);
            }, (errors) => {
                alert(error);
            });
        },
        createUserSubscription : ({commit}, data) => {
            Vue.http.post(action('User\Subscription\UserSubscriptionController@store'), data).then((response) => {
                userSubscriptionStore.dispatch('getUserSubscription');
            }, (errors) => {
                alert(error);
            });
        },
        cancelSubscription : ({commit}, subscription_id) => {
            Vue.http.delete(action('User\Subscription\UserSubscriptionController@destroy', {subscription: subscription_id})).then((response) => {
                userSubscriptionStore.dispatch('getUserSubscription');
            }, (errors) => {
                alert(error);
            });
        },
        getUserInvoices : ({commit}) => {
            Vue.http.get(action('User\Subscription\UserSubscriptionInvoiceController@index')).then((response) => {
                commit('SET_USER_INVOICES', response.data);
            }, (errors) => {
                alert(error);
            })
        }
    },
    mutations: {
        SET_USER_SUBSCRIPTION: (state, subscription) => {
            if (!_.isEmpty(subscription)) {
                state.valid_subscription = true;
                userSubscriptionStore.dispatch('getUpcomingSubscription');
            } else {
                state.valid_subscription = false;
            }

            state.user_subscription = subscription;
        },
        SET_USER_UPCOMING_SUBSCRIPTION : (state, upcoming_subscription) => {
            state.user_upcoming_subscription = upcoming_subscription;
        },
        SET_USER_INVOICES : (state, invoices) => {
            state.user_invoices = invoices;
        }
    }
}
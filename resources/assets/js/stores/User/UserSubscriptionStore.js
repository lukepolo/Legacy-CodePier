import Vue from "vue/dist/vue";
import Vuex from "vuex";
import {action} from ".././helpers";

Vue.use(Vuex);

const userSubscriptionStore = new Vuex.Store({
    state: {
        user_subscription : null,
        user_upcoming_subscription : null
    },
    actions: {
        getUserSubscription: ({commit}) => {
            Vue.http.get(action('User\Subscription\UserSubscriptionController@index')).then((response) => {
                commit('SET_USER_SUBSCRIPTION', response.json());
            }, (errors) => {
                alert(error);
            })
        },
        getUpcomingSubscription : ({commit}) => {
            Vue.http.get(this.action('User\Subscription\UserSubscriptionUpcomingInvoiceController@index')).then((response) => {
                commit('SET_USER_UPCOMING_SUBSCRIPTION', response.json());
            }, (errors) => {
                alert(error);
            });
        },
        createUserSubscription : ({commit}, data) => {
            Vue.http.post(this.action('User\Subscription\UserSubscriptionController@store'), data).then((response) => {
                userSubscriptionStore.dispatch('getUserSubscription');
            }, (errors) => {
                alert(error);
            });
        },
        cancelSubscription : ({commit}, subscription_id) => {
            Vue.http.delete(action('User\Subscription\UserSubscriptionController@destroy', {subscription: subscription.id})).then((response) => {
                userSubscriptionStore.dispatch('getUserSubscription');
            }, (errors) => {
                alert(error);
            });
        }
    },
    mutations: {
        SET_USER_SUBSCRIPTION: (state, user_subscription) => {
            state.user_subscription = user_subscription;
        },
        SET_USER_UPCOMING_SUBSCRIPTION : (state, user_upcoming_subscription) => {
            state.user_upcoming_subscription = user_upcoming_subscription;
        }
    }
});

export default userSubscriptionStore
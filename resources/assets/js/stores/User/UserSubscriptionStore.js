import Vue from "vue/dist/vue";
import Vuex from "vuex";
import {action} from ".././helpers";

Vue.use(Vuex);

const userSubscriptionStore = new Vuex.Store({
    state: {
        user_subscriptions : []
    },
    actions: {
        getUserSubscription: ({commit}) => {
            Vue.http.get(action('User\Subscription\UserSubscriptionController@index')).then((response) => {
                commit.set('SET_USER_SUBSCRIPTIONS', response.json());
            }, (errors) => {
                alert(error);
            })
        },
        getUpcomingSubscription : ({commit}) => {
            // Vue.http.get(this.action('User\Subscription\UserSubscriptionUpcomingInvoiceController@index')).then((response) => {
            //     this.upcomingSubscription = response.json();
            // }, (errors) => {
            //     alert(error);
            // });
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
        SET_USER_SUBSCRIPTIONS: (state, user_subscriptions) => {
            state.user_subscriptions = user_subscriptions;
        }
    }
});

export default userSubscriptionStore
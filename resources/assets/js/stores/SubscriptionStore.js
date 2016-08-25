import Vue from "vue/dist/vue";
import Vuex from "vuex";
import {action} from "./helpers";

Vue.use(Vuex);

const subscriptionStore = new Vuex.Store({
    state: {
        subscriptions: [],
    },
    actions: {
        getSubscriptions: ({commit}) => {
            Vue.http.get(action('SubscriptionController@index')).then((response) => {
                commit('SET_SUBSCRIPTIONS', response.json());
            }, (errors) => {
                alert(error);
            });
        }
    },
    mutations: {
        SET_SUBSCRIPTIONS: (state, subscriptions) => {
            state.subscriptions = subscriptions;
        }
    }
});

export default subscriptionStore
import Vue from "vue/dist/vue";
import Vuex from "vuex";
import {action} from "./helpers";

Vue.use(Vuex);

const subscriptionStore = new Vuex.Store({
    state: {
        plans: [],
    },
    actions: {
        getPlans: ({commit}) => {
            Vue.http.get(action('SubscriptionController@index')).then((response) => {
                commit('SET_PLANS', response.json());
            }, (errors) => {
                alert(error);
            });
        }
    },
    mutations: {
        SET_PLANS: (state, plans) => {
            state.plans = plans;
        }
    }
});

export default subscriptionStore
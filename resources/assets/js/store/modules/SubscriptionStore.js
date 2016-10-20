export default {
    state: {
        plans: [],
    },
    actions: {
        getPlans: ({commit}) => {
            Vue.http.get(action('SubscriptionController@index')).then((response) => {
                commit('SET_PLANS', response.data);
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
}
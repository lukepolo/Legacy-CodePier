export default {
    state: {
        piles: [],
        user_piles: [],
    },
    actions: {
        getPiles: ({commit, dispatch}) => {
            Vue.http.get(Vue.action('Pile\PileController@index')).then((response) => {
                commit('SET_PILES', response.data);
            }, (errors) => {
                alert('handle some error')
            });
        },
        getUserPiles: ({commit}) => {
            Vue.http.get(Vue.action('Pile\PileController@index', {all: true})).then((response) => {
                commit('SET_USER_PILES', response.data);
            }, (errors) => {
                alert('handle some error')
            });
        },
        createPile: ({dispatch}, data) => {
            Vue.http.post(Vue.action('Pile\PileController@store'), data).then((response) => {
                dispatch('getPiles');
            }, (errors) => {
                alert(error);
            })
        },
        changePiles : ({commit}, pileId) => {
            Vue.http.post(Vue.action('Pile\PileController@changePile'), {pile : pileId}).then((response) => {
                commit('SET_USER', response.data);
            }, (errors) => {
                alert(error);
            })
        },
        updatePile: ({dispatch}, data) => {
            Vue.http.put(Vue.action('Pile\PileController@update', {pile: data.pile.id}), data).then((response) => {
                dispatch('getPiles');
            }, (errors) => {
                alert(error);
            })
        },
        deletePile: ({dispatch}, pile) => {
            Vue.http.delete(Vue.action('Pile\PileController@destroy', {pile: pile})).then((response) => {
                dispatch('getPiles');
            }, (errors) => {
                alert(error);
            })
        }
    },
    mutations: {
        SET_USER_PILES: (state, piles) => {
            state.user_piles = piles;
        },
        SET_PILES: (state, piles) => {
            state.piles = piles;
        }
    }
}
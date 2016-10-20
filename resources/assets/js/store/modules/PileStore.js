export default {
    state: {
        piles: [],
        user_piles: [],
        currentPile: null,
        current_pile_id: parseInt(localStorage.getItem('current_pile_id'))
    },
    actions: {
        getPiles: ({commit, getter}) => {
            Vue.http.get(Vue.action('Pile\PileController@index')).then((response) => {
                commit('SET_PILES', response.data);
            }, (errors) => {
                alert('handle some error')
            });
        },
        getUserPiles: ({commit}) => {
            Vue.http.get(Vue.action('Pile\PileController@index', { all : true })).then((response) => {
                commit('SET_USER_PILES', response.data);
            }, (errors) => {
                alert('handle some error')
            });
        },
        setCurrentPile: ({commit, state}) => {
            var current_pile = _.find(state.piles, (pile) => {
                return pile.id == state.current_pile_id;
            });

            commit('SET_CURRENT_PILE', current_pile);
        },
        setCurrentPileID: ({commit}, pile_id) => {
            localStorage.setItem('current_pile_id', pile_id);
            commit('SET_CURRENT_PILE_ID', pile_id);
        },
        createPile : ({dispatch}, data) => {
            Vue.http.post(Vue.action('Pile\PileController@store'), data).then((response) => {
                dispatch('getPiles');
            }, (errors) => {
                alert(error);
            })
        },
        updatePile: ({dispatch}, data) => {
            Vue.http.put(Vue.action('Pile\PileController@update', { pile : data.pile.id }), data).then((response) => {
                dispatch('getPiles');
            }, (errors) => {
                alert(error);
            })
        },
        deletePile: ({dispatch}, pile) => {
            Vue.http.delete(Vue.action('Pile\PileController@destroy', { pile : pile })).then((response) => {
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
        SET_PILES: ({dispatch}, state, piles) => {
            state.piles = piles;
            dispatch('setCurrentPile');
        },
        SET_CURRENT_PILE: (state, pile) => {
            state.currentPile = pile;
        },
        SET_CURRENT_PILE_ID: ({dispatch}, state, pile_id) => {
            state.current_pile_id = parseInt(pile_id);
            dispatch('setCurrentPile');
        }
    }
}
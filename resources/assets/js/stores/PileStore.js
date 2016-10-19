import Vue from "vue/dist/vue";
import Vuex from "vuex";
import {action} from "./helpers";

export default new Vuex.Store({
    state: {
        piles: [],
        user_piles: [],
        currentPile: null,
        current_pile_id: parseInt(localStorage.getItem('current_pile_id'))
    },
    actions: {
        getPiles: ({commit}) => {
            Vue.http.get(action('Pile\PileController@index')).then((response) => {
                commit('SET_PILES', response.data);
            }, (errors) => {
                alert('handle some error')
            });
        },
        getUserPiles: ({commit}) => {
            Vue.http.get(action('Pile\PileController@index', { all : true })).then((response) => {
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
        createPile : ({commit}, data) => {
            Vue.http.post(action('Pile\PileController@store'), data).then((response) => {
                pileStore.dispatch('getPiles');
            }, (errors) => {
                alert(error);
            })
        },
        updatePile: ({commit}, data) => {
            Vue.http.put(action('Pile\PileController@update', { pile : data.pile.id }), data).then((response) => {
                pileStore.dispatch('getPiles');
            }, (errors) => {
                alert(error);
            })
        },
        deletePile: ({commit}, pile) => {
            Vue.http.delete(action('Pile\PileController@destroy', { pile : pile })).then((response) => {
                pileStore.dispatch('getPiles');
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
            pileStore.dispatch('setCurrentPile');
        },
        SET_CURRENT_PILE: (state, pile) => {
            state.currentPile = pile;
        },
        SET_CURRENT_PILE_ID: (state, pile_id) => {
            state.current_pile_id = parseInt(pile_id);
            pileStore.dispatch('setCurrentPile');
        }
    }
});
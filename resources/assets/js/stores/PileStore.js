import Vue from "vue/dist/vue";
import Vuex from "vuex";
import {action} from "./helpers";

Vue.use(Vuex);

const pileStore = new Vuex.Store({
    state: {
        piles: [],
        currentPile: null,
        current_pile_id: localStorage.getItem('current_pile_id')
    },
    actions: {
        getPiles: ({commit}) => {
            Vue.http.get(action('Pile\PileController@index')).then((response) => {
                commit('SET_PILES', response.json());
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
        }
    },
    mutations: {
        SET_PILES: (state, piles) => {
            state.piles = piles;
            pileStore.dispatch('setCurrentPile');
        },
        SET_CURRENT_PILE: (state, pile) => {
            state.currentPile = pile;
        },
        SET_CURRENT_PILE_ID: (state, pile_id) => {
            state.current_pile_id = pile_id;
            pileStore.dispatch('setCurrentPile');
        }
    }
});

export default pileStore
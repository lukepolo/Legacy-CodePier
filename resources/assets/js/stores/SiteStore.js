import Vue from "vue/dist/vue";
import Vuex from "vuex";
import {action} from "./helpers";

Vue.use(Vuex);

const siteStore = new Vuex.Store({
    state: {
        sites: []
    },
    actions: {
        getSites: ({commit}) => {
            Vue.http.get(action('Pile\PileSitesController@show', {pile: pileStore.state.current_pile_id})).then((response) => {
                commit('SET_SITES', response.json());
            }, (errors) => {
                alert(error);
            });
        }
    },
    mutations: {
        SET_SITES: (state, sites) => {
            state.sites = sites;
        }
    }
});

export default siteStore
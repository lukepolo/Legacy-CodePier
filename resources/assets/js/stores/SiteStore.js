import Vue from "vue/dist/vue";
import Vuex from "vuex";
import {action} from "./helpers";

Vue.use(Vuex);

const siteStore = new Vuex.Store({
    state: {
        sites: [],
        site: null
    },
    actions: {
        getSite: ({commit}, site_id) => {
            Vue.http.get(action('Site\SiteController@show', {site: site_id})).then((response) => {
                commit('SET_SITE', response.json());
            }, (errors) => {
                alert(error);
            });
        },
        getSites: ({commit}) => {
            Vue.http.get(action('Pile\PileSitesController@show', {pile: pileStore.state.current_pile_id})).then((response) => {
                commit('SET_SITES', response.json());
            }, (errors) => {
                alert(error);
            });
        },
        updateSite: ({commit}, payload) => {

            console.log(payload);

            Vue.http.put(action('Site\SiteController@update', {site: payload.site_id}), payload.data).then((response) => {
                commit('SET_SITE', response.json());
            }, (errors) => {
                alert(error);
            });
        },
    },
    mutations: {
        SET_SITE: (state, site) => {
            state.site = site;
        },
        SET_SITES: (state, sites) => {
            state.sites = sites;
        }
    }
});

export default siteStore
import Vue from "vue/dist/vue";
import Vuex from "vuex";
import { action } from "./helpers";

Vue.use(Vuex);

const store = new Vuex.Store({
    state: {
        user: null,
        servers : [],
        current_pile_id: localStorage.getItem('current_pile_id')
    },
    actions: {
        getServers: ({commit}) => {
            Vue.http.get(action('Server\ServerController@index', {pile_id: localStorage.getItem('current_pile_id')})).then((response) => {
                commit('SET_SERVERS', response.json());
            }, (errors) => {
                alert('handle some error')
            });
        },
        getCurrentPile: () => {
            return localStorage.getItem('current_pile_id');
        }
    },
    mutations : {
        SET_SERVERS : (state, servers) => {
            state.servers = servers;
        }
    },
    setters: {
        setCurrentPile: (pile_id) => {
            localStorage.setItem('current_pile_id', pile_id);
            this.current_pile_id = pile_id;
        }
    },

    getters: {}
});

export default store
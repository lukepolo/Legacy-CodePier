import Vue from "vue/dist/vue";
import Vuex from "vuex";
import {action} from "./helpers";

Vue.use(Vuex);

const serverStore = new Vuex.Store({
    state: {
        servers: []
    },
    actions: {
        getServers: ({commit}) => {
            Vue.http.get(action('Server\ServerController@index', {pile_id: pileStore.state.current_pile_id})).then((response) => {
                commit('SET_SERVERS', response.json());
            }, (errors) => {
                alert('handle some error')
            });
        }
    },
    mutations: {
        SET_SERVERS: (state, servers) => {
            state.servers = servers;
        }
    }
});

export default serverStore
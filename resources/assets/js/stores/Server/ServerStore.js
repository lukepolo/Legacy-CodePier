import Vue from "vue/dist/vue";
import Vuex from "vuex";
import {action} from "./../helpers";

Vue.use(Vuex);

const serverStore = new Vuex.Store({
    state: {
        servers: []
    },
    actions: {
        getServers: ({commit}, callback) => {
            Vue.http.get(action('Server\ServerController@index', {pile_id: pileStore.state.current_pile_id})).then((response) => {
                commit('SET_SERVERS', response.json());
                typeof callback === 'function' && callback();
            }, (errors) => {
                alert('handle some error')
            });
        },
        createServer: ({commit}, data) => {
            Vue.http.post(action('Server\ServerController@store'), data).then((response) => {
                alert('open events tab');
            }, (errors) => {
                alert(error);
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
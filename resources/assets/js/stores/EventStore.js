import Vue from "vue/dist/vue";
import Vuex from "vuex";
import {action} from "./helpers";

Vue.use(Vuex);

const eventStore = new Vuex.Store({
    state: {
        events: [],
        events_pagination : null
    },
    actions: {
        getEvents: ({commit}, page) => {
            Vue.http.get(action('EventController@index', { page : page ? page : 1 })).then((response) => {
                commit('SET_EVENTS', response.data);
            }, (errors) => {
                alert('handle some error')
            });
        }
    },
    mutations: {
        SET_EVENTS: (state, events_pagination) => {
            _.forEach(events_pagination.data, function(event) {
                state.events.push(event);
            });

            state.events_pagination = events_pagination;
        }
    }
});

export default eventStore
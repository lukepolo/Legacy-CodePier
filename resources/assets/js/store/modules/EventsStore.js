export default {
    state: {
        events: [],
        events_pagination: null
    },
    actions: {
        getEvents: ({commit}, page) => {
            Vue.http.get(Vue.action('EventController@index', {page: page ? page : 1})).then((response) => {
                commit('SET_EVENTS', response.data);
            }, (errors) => {
                alert(errors);
            });
        }
    },
    mutations: {
        SET_EVENTS: (state, events_pagination) => {

            _.forEach(events_pagination.data, function (event) {
                state.events.push(event);
            });

            state.events_pagination = events_pagination;
        }
    }
}
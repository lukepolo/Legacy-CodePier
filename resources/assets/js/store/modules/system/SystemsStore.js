export default {
    state: {
        systems: null
    },
    actions: {
        getSystems: ({ commit }) => {
            Vue.http.get(Vue.action('SystemsController@index')).then((response) => {
                commit('SET_SYSTEMS', response.data)
            }, (errors) => {
                app.handleApiError(errors)
            })
        },
    },
    mutations: {
        SET_SYSTEMS: (state, systems) => {
            state.systems = systems
        },
    }
}

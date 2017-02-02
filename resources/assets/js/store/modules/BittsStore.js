export default {
    state: {
        bitts: []
    },
    actions: {
        getBitts: ({ commit }) => {
            Vue.http.get(Vue.action('BittsController@index')).then((response) => {
                commit('SET_BITTS', response.data)
            }, (errors) => {
                app.handleApiError(errors)
            })
        },
        createBitt: ({ commit }, data) => {
            Vue.http.post(Vue.action('BittsController@store'), data).then((response) => {
                commit('ADD_BITT', response.data)
            }, (errors) => {
                app.handleApiError(errors)
            })
        },
        updateBitt: ({ commit }, data) => {
            Vue.http.put(Vue.action('BittsController@update', { bitt: bitt }, data)).then((response) => {
                commit('UPDATE_BITT', response.data)
            }, (errors) => {
                app.handleApiError(errors)
            })
        },
        deleteBitt: ({ commit }, bitt) => {
            Vue.http.delete(Vue.action('BittsController@destroy', { bitt: bitt })).then((response) => {
                commit('REMOVE_BITT', response.data)
            }, (errors) => {
                app.handleApiError(errors)
            })
        }
    },
    mutations: {
        SET_BITTS: (state, bitts) => {
            state.bitts = bitts
        },
        ADD_BITT: (state, bitt) => {
            state.bitts.push(bitt)
        },
        UPDATE_BITT: (state, bitt) => {
            Vue.set(state, _.findKey(state.bitts, { id: bitt.id }), bitt)
        },
        REMOVE_BITT: (state, bittId) => {
            Vue.set(state, 'bitts', _.reject(state.bitts, { id: bittId }))
        }
    }
}

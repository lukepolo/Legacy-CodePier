export default {
    state: {
        bitts: [],
        bitt: null
    },
    actions: {
        getBitt: ({ commit }, bitt) => {
            return Vue.http.get(Vue.action('BittsController@show', { bitt: bitt })).then((response) => {
                commit('SET_BITT', response.data)
                return response.data
            }, (errors) => {
                app.handleApiError(errors)
            })
        },
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
                app.$router.push({ name: 'bitts_market_place' })
            }, (errors) => {
                app.handleApiError(errors)
            })
        },
        updateBitt: ({ commit }, data) => {
            console.info(data);
            Vue.http.put(Vue.action('BittsController@update', { bitt: data.bitt }), data.form).then((response) => {
                commit('UPDATE_BITT', response.data)
                app.$router.push({ name: 'bitts_market_place' })
            }, (errors) => {
                app.handleApiError(errors)
            })
        },
        deleteBitt: ({ commit }, bitt) => {
            Vue.http.delete(Vue.action('BittsController@destroy', { bitt: bitt })).then((response) => {
                commit('REMOVE_BITT', bitt)
            }, (errors) => {
                app.handleApiError(errors)
            })
        }
    },
    mutations: {
        SET_BITT: (state, bitt) => {
            state.bitt = bitt
        },
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
            Vue.set(state.bitts, 'data', _.reject(state.bitts.data, { id: bittId }))
        }
    }
}

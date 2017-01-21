export default {
    state: {
        buoy: null,
        buoys: null
    },
    actions: {
        getBuoy: ({ commit }, buoy) => {
            Vue.http.get(Vue.action('BuoyAppController@show', { buoy: buoy })).then((response) => {
                commit('SET_BUOY', response.data)
            }, (errors) => {
                app.handleApiError(errors)
            })
        },
        getBuoys: ({ commit }) => {
            Vue.http.get(Vue.action('BuoyAppController@index')).then((response) => {
                commit('SET_BUOYS', response.data)
            }, (errors) => {
                app.handleApiError(errors)
            })
        },
        createBuoy: ({ commit }, data) => {
            Vue.http.post(Vue.action('BuoyAppController@store'), data).then((response) => {
                commit('ADD_BUOY', response.data)
                app.$router.push({ name: 'buoy_market_place' })
            }, (errors) => {
                app.handleApiError(errors)
            })
        },
        updateBuoy: ({ commit }, data) => {
            Vue.http.put(Vue.action('BuoyAppController@update', { buoy: data.buoy }), data).then((response) => {
                commit('SET_BUOY', response.data)
                app.$router.push({ name: 'buoy_market_place' })
            }, (errors) => {
                app.handleApiError(errors)
            })
        },
        deleteBuoy: ({ commit }, buoy) => {
            Vue.http.delete(Vue.action('BuoyAppController@destroy', { buoy: buoy }), data).then(() => {
                commit('REMOVE_BUOY', buoy)
            }, (errors) => {
                app.handleApiError(errors)
            })
        }
    },
    mutations: {
        SET_BUOY: (state, buoy) => {
            state.buoy = buoy
        },
        SET_BUOYS: (state, buoys) => {
            state.buoys = buoys
        },
        REMOVE_BUOY: (state, buoyId) => {
            Vue.set(state, 'buoys', _.reject(state.buoy, { id: buoyId }))
        },
        ADD_BUOY: (state, buoy) => {
            state.buoys.push(buoy)
        }
    }
}

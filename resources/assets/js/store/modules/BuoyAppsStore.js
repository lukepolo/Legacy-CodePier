export default {
    state: {
        buoy: null,
        buoys: [],
        buoy_classes: null
    },
    actions: {
        getBuoy: ({ commit }, buoy) => {
            return Vue.http.get(Vue.action('BuoyAppController@show', { buoy: buoy })).then((response) => {
                commit('SET_BUOY', response.data)
                return response.data
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
        updateBuoy: ({ commit }, data) => {
            Vue.http.post(Vue.action('BuoyAppController@update', { buoy: data.buoy }), data.form, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            }).then((response) => {
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
        },
        getBuoyClasses: ({ commit }) => {
            return Vue.http.get(Vue.action('BuoyAppController@getBuoyClasses')).then((response) => {
                commit('SET_BUOY_CLASSES', response.data)
                return response.data
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
        SET_BUOY_CLASSES: (state, buoyClasses) => {
            state.buoy_classes = buoyClasses
        }
    }
}

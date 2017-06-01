export default {
    state: {
        buoy_apps: [],
        buoy_app: null
    },
    actions: {
        getBuoy: ({ commit }, buoyApp) => {
            return Vue.http.get(Vue.action('BuoyAppController@show', { buoy_app: buoyApp })).then((response) => {
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
            Vue.http.post(Vue.action('BuoyAppController@update', { buoy_app: data.buoy_app }), data.form, {
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
        deleteBuoy: ({ commit }, buoyApp) => {
            Vue.http.delete(Vue.action('BuoyAppController@destroy', { buoy_app: buoyApp }), data).then(() => {
                commit('REMOVE_BUOY', buoy_app)
            }, (errors) => {
                app.handleApiError(errors)
            })
        },
        installBuoyOnServer: ({ commit }, data) => {
            Vue.http.post(Vue.action('Server\ServerBuoyController@store', { server: data.server }), data).then((response) => {
            }, (errors) => {
                app.handleApiError(errors)
            })
        }
    },
    mutations: {
        SET_BUOY: (state, buoyApp) => {
            state.buoy_app = buoyApp
        },
        SET_BUOYS: (state, buoyApps) => {
            state.buoy_apps = buoyApps
        },
        REMOVE_BUOY: (state, buoyAppId) => {
            Vue.set(state, 'buoy_apps', _.reject(state.buoy_app, { id: buoyAppId }))
        }
    }
}

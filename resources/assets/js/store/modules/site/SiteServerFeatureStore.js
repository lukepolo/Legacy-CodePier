export default {
  state: {
    site_server_features: {}
  },
  actions: {
    getSiteServerFeatures: ({ commit }, site) => {
      Vue.http.get(Vue.action('Site\SiteServerFeaturesController@index', { site: site })).then((response) => {
        commit('SET_SITE_SERVER_FEATURES', response.data)
      }, (errors) => {
        app.handleApiError(errors)
      })
    },
    updateSiteServerFeatures: ({ dispatch }, data) => {
      Vue.http.post(Vue.action('Site\SiteServerFeaturesController@update', { site: data.site }), data.form).then(() => {
        dispatch('getSite', data.site)
      }, (errors) => {
        app.handleApiError(errors)
      })
    }
  },
  mutations: {
    SET_SITE_SERVER_FEATURES: (state, serverFeatures) => {
      state.site_server_features = serverFeatures
    }
  }
}

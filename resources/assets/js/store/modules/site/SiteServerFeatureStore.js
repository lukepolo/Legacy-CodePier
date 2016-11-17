export default {
    state: {
        suggestedFeatures :  {}
    },
    actions: {
        getSiteSuggestedFeatures: ({commit}, siteId) => {
            Vue.http.get(Vue.action('Server\ServerFeatureController@getSuggestedFeatures', {site: siteId})).then((response) => {
                commit('SET_SITE_SUGGESTED_FEATURES', response.data);
            }, (errors) => {
                app.showError(error);
            });
        },
        updateSiteServerFeatures: ({commit, dispatch}, data) => {
            Vue.http.post(Vue.action('Site\SiteController@updateSiteServerFeatures', {site: data.site}), data.form).then((response) => {
                dispatch('getSite', data.site);
            }, (errors) => {
                app.showError(error);
            });
        }
    },
    mutations: {
        SET_SITE_SUGGESTED_FEATURES : (state, features) => {
            state.suggestedFeatures = features;
        }
    }
}
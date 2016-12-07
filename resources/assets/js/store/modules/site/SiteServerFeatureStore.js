export default {
    state: {
        suggestedFeatures :  {}
    },
    actions: {
        getSiteSuggestedFeatures: ({commit}, siteId) => {
            Vue.http.get(Vue.action('Site\SiteFeatureController@getSuggestedFeatures', {site: siteId})).then((response) => {
                commit('SET_SITE_SUGGESTED_FEATURES', response.data);
            }, (errors) => {
                app.showError(errors);
            });
        },
        updateSiteServerFeatures: ({commit, dispatch}, data) => {
            Vue.http.post(Vue.action('Site\SiteController@updateSiteServerFeatures', {site: data.site}), data.form).then((response) => {
                dispatch('getSite', data.site);
            }, (errors) => {
                app.showError(errors);
            });
        }
    },
    mutations: {
        SET_SITE_SUGGESTED_FEATURES : (state, features) => {
            state.suggestedFeatures = features;
        }
    }
}
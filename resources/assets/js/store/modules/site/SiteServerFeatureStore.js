export default {
    state: {

    },
    actions: {
        updateSiteServerFeatures: ({commit, dispatch}, data) => {
            Vue.http.post(Vue.action('Site\SiteController@updateSiteServerFeatures', {site: data.site}), data.form).then((response) => {
                dispatch('getSite', data.site);
            }, (errors) => {
                app.showError(error);
            });
        }
    },
    mutations: {

    }
}
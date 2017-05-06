export default {
    state: {
        site_language_settings: [],
        available_language_settings: []
    },
    actions: {
        getSiteLanguageSettings: ({ commit }, site) => {
            Vue.http.get(Vue.action('Site\SiteLanguageSettingsController@index', { site : site })).then((response) => {
                commit('SET_SITE_LANGUAGE_SETTINGS', response.data)
            })
        },
        getAvailableLanguageSettings: ({ commit }, site) => {
            Vue.http.get(Vue.action('Site\SiteLanguageSettingsController@getLanguageSettings', { site : site })).then((response) => {
                commit('SET_AVAILABLE_LANGUAGE_SETTINGS', response.data)
            })

        },
        updateSiteFile: ({ commit }, data) => {
            // Vue.http.put(Vue.action('Site\SiteFileController@update', {
            //     site: data.site,
            //     file: data.file_id
            // }), {
            //     file_path: data.file,
            //     content: data.content
            // }).then((response) => {
            //     app.showSuccess('You have updated the file')
            // }, (errors) => {
            //     app.handleApiError(errors)
            // })
        },
    },
    mutations: {
        SET_SITE_LANGUAGE_SETTINGS: (state, language_settings) => {
            state.site_language_settings = language_settings
        },
        SET_AVAILABLE_LANGUAGE_SETTINGS: (state, language_settings) => {
            state.available_language_settings = language_settings
        },
    }
}

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
        getSiteAvailableLanguageSettings: ({ commit }, site) => {
            Vue.http.get(Vue.action('Site\SiteLanguageSettingsController@getLanguageSettings', { site : site })).then((response) => {
                commit('SET_AVAILABLE_LANGUAGE_SETTINGS', response.data)
            })

        },
        runSiteLanguageSetting: ({}, data) => {
            Vue.http.post(Vue.action('Site\SiteLanguageSettingsController@store', {
                site: data.site,
            }), {
                params : data.params,
                setting : data.setting,
                language : data.language,
            }).then((response) => {
                app.showSuccess('You have updated the settings')
            }, (errors) => {
                app.handleApiError(errors)
            })
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

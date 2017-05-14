export default {
    state: {
        server_language_settings: [],
        available_language_settings: []
    },
    actions: {
        getServerLanguageSettings: ({ commit }, server) => {
            Vue.http.get(Vue.action('Server\ServerLanguageSettingsController@index', { server : server })).then((response) => {
                commit('SET_SERVER_LANGUAGE_SETTINGS', response.data)
            })
        },
        getServerAvailableLanguageSettings: ({ commit }, server) => {
            Vue.http.get(Vue.action('Server\ServerLanguageSettingsController@getLanguageSettings', { server : server })).then((response) => {
                commit('SET_AVAILABLE_LANGUAGE_SETTINGS', response.data)
            })
        },
        runServerLanguageSetting: ({ }, data) => {
            Vue.http.post(Vue.action('Server\ServerLanguageSettingsController@store', {
                server: data.server,
            }), {
                params : data.params,
                setting : data.setting,
                language : data.language,
            }).then(() => {
                app.showSuccess('You have updated the settings')
            }, (errors) => {
                app.handleApiError(errors)
            })
        },
    },
    mutations: {
        SET_SERVER_LANGUAGE_SETTINGS: (state, language_settings) => {
            state.server_language_settings = language_settings
        },
        SET_AVAILABLE_LANGUAGE_SETTINGS: (state, language_settings) => {
            state.available_language_settings = language_settings
        },
    }
}

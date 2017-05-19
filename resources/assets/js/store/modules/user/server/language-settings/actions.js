export const get = ({}, server) => {
    return Vue.request().get(
        Vue.action('Server\ServerLanguageSettingsController@index', { server: server }),
        'user_server_language_settings/setAll'
    )
}

export const getAvailable = ({}, server) => {
    Vue.request().get(
        Vue.action('Server\ServerLanguageSettingsController@getLanguageSettings', { server: server }),
        'user_server_language_settings/setAvailableLanguageSettings'
    )
}

export const run = ({}, data) => {
    Vue.request(data).post(
            Vue.action('Server\ServerLanguageSettingsController@store', {
                server: data.server
            })
    ).then(() => {
        app.showSuccess('You have updated the settings')
    })
}

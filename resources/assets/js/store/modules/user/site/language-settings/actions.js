export const get = ({}, site) => {
    return Vue.request().get(
        Vue.action('Site\SiteLanguageSettingsController@index', { site: site }),
        'user_site_language_settings/setAll'
    )
}

export const getAvailable = ({}, site) => {
    Vue.request().get(
        Vue.action('Site\SiteLanguageSettingsController@getLanguageSettings', { site: site }),
        'user_site_language_settings/setAvailableLanguageSettings'
    )
}

export const run = ({}) => {
    Vue.request(data).post(
            Vue.action('Site\SiteLanguageSettingsController@store', {
                site: data.site
            })
    ).then(() => {
        app.showSuccess('You have updated the settings')
    })
}

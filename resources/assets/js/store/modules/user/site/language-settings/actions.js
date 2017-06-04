export const get = (context, site) => {
    return Vue.request().get(
        Vue.action('Site\SiteLanguageSettingsController@index', { site: site }),
        'user_site_language_settings/setAll'
    )
}

export const getAvailable = (context, site) => {
    Vue.request().get(
        Vue.action('Site\SiteLanguageSettingsController@getLanguageSettings', { site: site }),
        'user_site_language_settings/setAvailableLanguageSettings'
    )
}

export const run = (context, data) => {
    Vue.request(data).post(
            Vue.action('Site\SiteLanguageSettingsController@store', {
                site: data.site
            })
    ).then(() => {
        app.showSuccess('You have updated the settings')
    })
}

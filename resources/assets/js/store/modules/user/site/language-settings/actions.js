export const get = (context, site) => {
    return Vue.request().get(
        Vue.action('SiteSiteLanguageSettingsController@index', { site: site }),
        'user_site_language_settings/setAll',
    );
};

export const getAvailable = (context, site) => {
    Vue.request().get(
        Vue.action('SiteSiteLanguageSettingsController@getLanguageSettings', {
            site: site,
        }),
        'user_site_language_settings/setAvailableLanguageSettings',
    );
};

export const run = (context, data) => {
    Vue.request(data)
        .post(
            Vue.action('SiteSiteLanguageSettingsController@store', {
                site: data.site,
            }),
        )
        .then(() => {
            app.showSuccess('You have updated the settings');
        });
};

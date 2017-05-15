export const get = ({}, site) => {
    return Vue.request().get(
        Vue.action('Site\SiteServerFeaturesController@index', { site: site }),
        'user_site_server_features/setAll'
    )
}

export const update = ({}, data) => {
    return Vue.request(data).patch(
        Vue.action('Site\SiteServerFeaturesController@update', { site: data.site_id }),
        'user_site_server_features/setAll'
    ).then(() => {
        app.showSuccess('Updated your site\'s server features')
    })
}

export const get = (context, data) => {

    if(data.server_type) {
        return Vue.request().get(
            Vue.action('Site\SiteServerFeaturesController@show', data),
            'user_site_server_features/setAll'
        )
    } else {
        return Vue.request().get(
            Vue.action('Site\SiteServerFeaturesController@index', data),
            'user_site_server_features/setAll'
        )
    }

}

export const update = (context, data) => {
    return Vue.request(data).post(
        Vue.action('Site\SiteServerFeaturesController@store', { site: data.site_id }),
        'user_site_server_features/setAll'
    ).then(() => {
        app.showSuccess('Updated your site\'s server features')
    })
}

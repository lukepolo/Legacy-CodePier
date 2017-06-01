export const get = (context, site) => {
    return Vue.request().get(
        Vue.action('Site\SiteSchemaController@index', { site: site }),
        'user_site_schemas/setAll'
    )
}

export const store = (context, data) => {
    return Vue.request(data).post(
        Vue.action('Site\SiteSchemaController@store', { site: data.site }),
        'user_site_schemas/add'
    )
}

export const destroy = (context, data) => {
    return Vue.request(data).delete(
        Vue.action('Site\SiteSchemaController@destroy', {
            site: data.site,
            schema: data.schema
        }),
        'user_site_schemas/remove'
    )
}

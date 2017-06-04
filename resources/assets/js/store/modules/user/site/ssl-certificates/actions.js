export const get = (context, site) => {
    return Vue.request().get(
        Vue.action('Site\SiteSslController@index', { site: site }),
        'user_site_ssl_certificates/setAll'
    )
}

export const store = (context, data) => {
    return Vue.request(data).post(
        Vue.action('Site\SiteSslController@store', { site: data.site_id }),
        'user_site_ssl_certificates/add'
    )
}

export const update = (context, data) => {
    return Vue.request(data).patch(
        Vue.action('Site\SiteSslController@update', { site: data.site, ssl_certificate: data.ssl_certificate }),
        'user_site_ssl_certificates/update'
    )
}

export const destroy = (context, data) => {
    return Vue.request(data).delete(
        Vue.action('Site\SiteSslController@destroy', { site: data.site, ssl_certificate: data.ssl_certificate }),
        'user_site_ssl_certificates/remove'
    )
}

export const get = (context, site) => {
    return Vue.request(site).get(
        Vue.action('Site\SiteServerController@index', { site: site }),
        'user_site_servers/setAll'
    )
}

export const updateLinks = ({ dispatch }, data) => {
    return Vue.request(data).post(
        Vue.action('Site\SiteServerController@store', { site: data.site })
    ).then(() => {
        dispatch('get', data.site)
    })
}

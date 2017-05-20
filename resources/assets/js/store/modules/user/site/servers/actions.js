export const get = ({}, site) => {
    return Vue.request(site).get(
        Vue.action('Site\SiteServerController@index', { site: site }),
        'user_site_servers/setAll'
    )
}
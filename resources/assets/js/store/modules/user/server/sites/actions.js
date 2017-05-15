export const get = ({}, server) => {
    return Vue.request().get(
        Vue.action('Server\ServerSiteController@index', { server: server }),
        'user_server_sites/setAll'
    )
}
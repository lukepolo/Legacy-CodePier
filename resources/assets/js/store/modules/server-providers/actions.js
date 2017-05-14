export const get = ({}, data) => {
    return Vue.request(data).get(
        Vue.action('Auth\Providers\ServerProvidersController@index'),
        'server_providers/setAll'
    )
}
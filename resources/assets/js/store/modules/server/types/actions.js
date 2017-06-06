export const get = () => {
    return Vue.request().get(
        Vue.action('Server\ServerTypesController@index'),
        'server_types/setAll'
    )
}

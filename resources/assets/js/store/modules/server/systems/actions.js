export const get = (context, data) => {
    return Vue.request(data).get(
        Vue.action('SystemsController@index'),
        'server_systems/setAll'
    )
}


export const get = () => {
    return Vue.request().get(
        Vue.action('Server\ServerFeatureController@getFrameworks'),
        'server_frameworks/setAll'
    )
}

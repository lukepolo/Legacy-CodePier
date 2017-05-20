export const get = () => {
    return Vue.request().get(
        Vue.action('Server\ServerFeatureController@getFrameworks'),
        'system_frameworks/setAll'
    )
}

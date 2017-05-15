export const get = () => {
    return Vue.request().get(
        Vue.action('Server\ServerFeatureController@getFeatures'),
        'system_frameworks/setAll'
    )
}
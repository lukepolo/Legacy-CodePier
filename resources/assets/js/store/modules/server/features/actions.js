export const get = () => {
    return Vue.request().get(
        Vue.action('Server\ServerFeatureController@getFeatures'),
        'server_features/setAll'
    )
}

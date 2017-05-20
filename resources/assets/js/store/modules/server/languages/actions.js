export const get = () => {
    return Vue.request().get(
        Vue.action('Server\ServerFeatureController@getLanguages'),
        'server_languages/setAll'
    )
}

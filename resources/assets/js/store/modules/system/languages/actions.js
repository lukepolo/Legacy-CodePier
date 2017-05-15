export const get = () => {
    return Vue.request().get(
        Vue.action('Server\ServerFeatureController@getLanguages'),
        'system_languages/setAll'
    )
}
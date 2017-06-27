export const get = () => {
    return Vue.request().get(
        Vue.action('ServerServerFeatureController@getLanguages'),
        'server_languages/setAll',
    );
};

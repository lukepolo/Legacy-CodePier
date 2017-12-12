export const get = () => {
  return Vue.request().get(
    Vue.action('ServerServerFeatureController@getFrameworks'),
    'server_frameworks/setAll'
  );
};

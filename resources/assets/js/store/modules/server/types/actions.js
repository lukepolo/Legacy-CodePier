export const get = () => {
  return Vue.request().get(
    Vue.action('ServerServerTypesController@index'),
    'server_types/setAll',
  );
};

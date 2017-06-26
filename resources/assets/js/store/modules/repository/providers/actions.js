export const get = (context, data) => {
  return Vue.request(data).get(
    Vue.action('AuthProvidersRepositoryProvidersController@index'),
    'repository_providers/setAll',
  );
};

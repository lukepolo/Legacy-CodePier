export const get = (context, user) => {
  return Vue.request().get(
    Vue.action('UserProvidersUserServerProviderController@index', {
      user: user,
    }),
    'user_server_providers/setAll',
  );
};

export const destroy = (context, data) => {
  return Vue.request(data).delete(
    Vue.action('UserProvidersUserServerProviderController@destroy', {
      user: data.user,
      server_provider: data.server_provider,
    }),
    'user_server_providers/remove',
  );
};

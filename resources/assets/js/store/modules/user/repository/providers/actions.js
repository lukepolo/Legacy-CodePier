export const get = (context, user) => {
  return Vue.request().get(
    Vue.action("UserProvidersUserRepositoryProviderController@index", {
      user: user,
    }),
    "user_repository_providers/setAll",
  );
};

export const destroy = (context, data) => {
  return Vue.request(data).delete(
    Vue.action("UserProvidersUserRepositoryProviderController@destroy", {
      user: data.user,
      repository_provider: data.repository_provider,
    }),
    "user_repository_providers/remove",
  );
};

export const get = (context, server) => {
  return Vue.request().get(
    Vue.action('ServerServerFeatureController@index', { server: server }),
    'user_server_features/setAll',
  );
};

export const store = (context, data) => {
  return Vue.request(data).post(
    Vue.action('ServerServerFeatureController@store', { server: data.server }),
    'user_server_features/setAll',
  );
};

export const install = (context, data) => {
  return Vue.request(data)
    .post(
      Vue.action('ServerServerFeatureController@store', {
        server: data.server,
      }),
      {
        service: data.service,
        feature: data.feature,
        parameters: data.parameters,
      },
    )
    .then(() => {
      app.showSuccess('You have queued a server feature install');
    });
};

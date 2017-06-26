export const get = (context, server) => {
  return Vue.request().get(
    Vue.action('ServerServerEnvironmentVariablesController@index', {
      server: server,
    }),
    'user_server_environment_variables/setAll',
  );
};

export const store = (context, data) => {
  return Vue.request(data).post(
    Vue.action('ServerServerEnvironmentVariablesController@store', {
      server: data.server,
    }),
    'user_server_environment_variables/add',
  );
};

export const destroy = (context, data) => {
  return Vue.request(data).delete(
    Vue.action('ServerServerEnvironmentVariablesController@destroy', {
      server: data.server,
      environment_variable: data.environment_variable,
    }),
    'user_server_environment_variables/remove',
  );
};

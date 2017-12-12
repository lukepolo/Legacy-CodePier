export const get = (context, server) => {
  return Vue.request().get(
    Vue.action('ServerServerDaemonsController@index', { server: server }),
    'user_server_daemons/setAll'
  );
};

export const store = (context, data) => {
  return Vue.request(data).post(
    Vue.action('ServerServerDaemonsController@store', { server: data.server }),
    'user_server_daemons/add'
  );
};

export const patch = (data) => {
  return Vue.request(data).put(
    Vue.action('ServerServerDaemonsController@update', { server: data.server }),
    'user_server_daemons/update'
  );
};

export const destroy = (context, data) => {
  return Vue.request(data).delete(
    Vue.action('ServerServerDaemonsController@destroy', {
      server: data.server,
      daemon: data.daemon
    }),
    'user_server_daemons/remove'
  );
};

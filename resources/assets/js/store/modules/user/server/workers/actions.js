export const get = (context, server) => {
  return Vue.request().get(
    Vue.action('ServerServerWorkerController@index', { server: server }),
    'user_server_workers/setAll'
  );
};

export const store = (context, data) => {
  return Vue.request(data).post(
    Vue.action('ServerServerWorkerController@store', {
      server: data.server
    }),
    'user_server_workers/add'
  );
};

export const destroy = (context, data) => {
  return Vue.request(data).delete(
    Vue.action('ServerServerWorkerController@destroy', {
      server: data.server,
      worker: data.worker
    }),
    'user_server_workers/remove'
  );
};

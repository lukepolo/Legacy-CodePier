export const restart = (context, server) => {
  Vue.request()
    .post(
      Vue.action('ServerServerController@restartServer', { server: server }),
    )
    .then(() => {
      app.showSuccess('You have restarted your server');
    });
};
export const restartWebServices = (context, server) => {
  Vue.request()
    .post(
      Vue.action('ServerServerController@restartWebServices', {
        server: server,
      }),
    )
    .then(() => {
      app.showSuccess('You have restarted your web services');
    });
};

export const restartDatabases = (context, server) => {
  Vue.request()
    .post(
      Vue.action('ServerServerController@restartDatabases', { server: server }),
    )
    .then(() => {
      app.showSuccess('You have restarted your databases');
    });
};

export const restartWorkers = (context, server) => {
  Vue.request()
    .post(
      Vue.action('ServerServerController@restartWorkerServices', {
        server: server,
      }),
    )
    .then(() => {
      app.showSuccess('You have restarted your workers');
    });
};
